

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

const walletRoot = document.querySelector('#wallet-root');

if (walletRoot) {
    const status = walletRoot.querySelector('#wallet-status');
    const addressOutput = walletRoot.querySelector('#wallet-address');
    const chainOutput = walletRoot.querySelector('#wallet-chain');
    const connectButton = walletRoot.querySelector('#connect-wallet');
    const switchButton = walletRoot.querySelector('#switch-sepolia');
    const balanceOutput = walletRoot.querySelector('#wallet-balance');
    const balanceStatus = walletRoot.querySelector('#balance-status');
    const refreshBalanceButton = walletRoot.querySelector('#refresh-balance');
    const connectUrl = walletRoot.dataset.connectUrl;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    function setStatus(message, kind = 'info') {
        status.textContent = message;
        status.dataset.kind = kind;
    }

    function provider() {
        return window.ethereum;
    }

    function chainIdFromHex(hexChainId) {
        return Number.parseInt(hexChainId, 16);
    }

    function shortenedAddress(address) {
        return `${address.slice(0, 8)}...${address.slice(-6)}`;
    }

    function formatSepoliaBalance(weiHex) {
        const wei = BigInt(weiHex);
        const divisor = 1000000000000000000n;
        const whole = wei / divisor;
        const fraction = (wei % divisor).toString().padStart(18, '0').replace(/0+$/, '').slice(0, 6);

        return fraction ? `${whole}.${fraction}` : whole.toString();
    }

    function setBalanceUnknown(message = 'Balance unavailable') {
        if (balanceOutput) balanceOutput.textContent = message;
        if (balanceStatus) balanceStatus.textContent = 'Not retrieved';
    }

    async function refreshBalance(address) {
        const injected = provider();
        if (!injected || !address) {
            setBalanceUnknown('Not connected');
            return;
        }

        const hexChainId = await injected.request({ method: 'eth_chainId' }).catch(() => null);
        if (!hexChainId || chainIdFromHex(hexChainId) !== 11155111) {
            setBalanceUnknown('Wrong network');
            return;
        }

        if (balanceOutput) balanceOutput.textContent = 'Loading...';
        if (balanceStatus) balanceStatus.textContent = 'Querying Ethereum Sepolia';

        try {
            const weiHex = await injected.request({
                method: 'eth_getBalance',
                params: [address, 'latest'],
            });

            if (typeof weiHex !== 'string' || !/^0x[0-9a-f]+$/i.test(weiHex)) {
                throw new Error('The wallet provider returned an invalid balance response.');
            }

            if (balanceOutput) balanceOutput.textContent = `${formatSepoliaBalance(weiHex)} Sepolia test ETH`;
            if (balanceStatus) {
                balanceStatus.textContent = `Last refreshed ${new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}`;
            }
        } catch (error) {
            setBalanceUnknown('Unavailable');
            setStatus(error?.code === -32000
                ? 'Sepolia RPC is unavailable or rate-limited. Balance was not estimated.'
                : 'Unable to retrieve the Sepolia balance. No balance was fabricated.', 'error');
        }
    }

    async function persistWallet(address, chainId) {
        const response = await fetch(connectUrl, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({ address, chain_id: chainId }),
        });

        if (!response.ok) {
            const body = await response.json().catch(() => ({}));
            throw new Error(body.message || 'Unable to save the wallet link.');
        }

        return response.json();
    }

    async function refreshWallet(accountOverride) {
        const injected = provider();
        if (!injected) {
            setStatus('No compatible browser wallet was detected. Install or enable MetaMask or another EIP-1193 wallet.', 'error');
            addressOutput.textContent = 'Not connected';
            chainOutput.textContent = 'Unknown';
            switchButton.hidden = true;
            return;
        }

        try {
            const accounts = accountOverride ?? await injected.request({ method: 'eth_accounts' });
            const hexChainId = await injected.request({ method: 'eth_chainId' });
            const chainId = chainIdFromHex(hexChainId);

            if (!accounts.length) {
                setStatus('Wallet is disconnected.', 'info');
                addressOutput.textContent = 'Not connected';
                chainOutput.textContent = 'Ethereum network';
                switchButton.hidden = true;
                return;
            }

            const address = accounts[0];
            addressOutput.textContent = shortenedAddress(address);
            addressOutput.title = address;

            if (chainId !== 11155111) {
                chainOutput.textContent = `Wrong network (${chainId})`;
                setStatus('Wrong network. Switch the browser wallet to Ethereum Sepolia (11155111).', 'error');
                switchButton.hidden = false;
                setBalanceUnknown('Wrong network');
                return;
            }

            chainOutput.textContent = 'Ethereum Sepolia (11155111)';
            await persistWallet(address, chainId);
            setStatus('Connected to Ethereum Sepolia.', 'success');
            switchButton.hidden = true;
            await refreshBalance(address);
        } catch (error) {
            setStatus(error.message || 'Unable to read the browser wallet.', 'error');
        }
    }

    connectButton?.addEventListener('click', async () => {
        const injected = provider();
        if (!injected) {
            setStatus('No compatible browser wallet was detected.', 'error');
            return;
        }

        try {
            setStatus('Waiting for wallet approval...', 'pending');
            const accounts = await injected.request({ method: 'eth_requestAccounts' });
            await refreshWallet(accounts);
        } catch (error) {
            if (error?.code === 4001) {
                setStatus('Wallet connection was rejected. No data was saved.', 'pending');
            } else {
                setStatus(error.message || 'Wallet connection failed.', 'error');
            }
        }
    });

    switchButton?.addEventListener('click', async () => {
        const injected = provider();
        if (!injected) return;

        try {
            await injected.request({
                method: 'wallet_switchEthereumChain',
                params: [{ chainId: '0xaa36a7' }],
            });
        } catch (error) {
            if (error?.code === 4902) {
                try {
                    await injected.request({
                        method: 'wallet_addEthereumChain',
                        params: [{
                            chainId: '0xaa36a7',
                            chainName: 'Sepolia',
                            nativeCurrency: { name: 'Sepolia ETH', symbol: 'ETH', decimals: 18 },
                            rpcUrls: ['https://rpc.sepolia.org'],
                            blockExplorerUrls: ['https://sepolia.etherscan.io'],
                        }],
                    });
                } catch (addError) {
                    setStatus(addError.message || 'Unable to add Ethereum Sepolia.', 'error');
                }
            } else if (error?.code === 4001) {
                setStatus('Network switch was rejected.', 'pending');
            } else {
                setStatus(error.message || 'Unable to switch network.', 'error');
            }
        }
    });

    refreshBalanceButton?.addEventListener('click', async () => {
        const injected = provider();
        if (!injected) {
            setBalanceUnknown('Not connected');
            return;
        }

        const accounts = await injected.request({ method: 'eth_accounts' }).catch(() => []);
        await refreshBalance(accounts[0]);
    });

    provider()?.on?.('accountsChanged', (accounts) => refreshWallet(accounts));
    provider()?.on?.('chainChanged', () => refreshWallet());

    document.querySelectorAll('[data-remove-wallet]').forEach((button) => {
        button.addEventListener('click', async () => {
            const response = await fetch(button.dataset.removeWallet, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
            });

            if (response.ok) {
                button.closest('[data-wallet-profile]').remove();
                setStatus('Wallet link removed.', 'info');
            } else {
                setStatus('Unable to remove the wallet link.', 'error');
            }
        });
    });

    refreshWallet();
}

const transactionRoot = document.querySelector('#transaction-root');

if (transactionRoot) {
    const recipientInput = transactionRoot.querySelector('#recipient');
    const amountInput = transactionRoot.querySelector('#amount');
    const statusOutput = transactionRoot.querySelector('#transaction-status');
    const reviewButton = transactionRoot.querySelector('#review-transaction');
    const reviewPanel = transactionRoot.querySelector('#transaction-review');
    const approveButton = transactionRoot.querySelector('#approve-transaction');
    const resultPanel = transactionRoot.querySelector('#transaction-result');
    const resultStatus = transactionRoot.querySelector('#transaction-result-status');
    const explorerLink = transactionRoot.querySelector('#transaction-explorer');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    const walletProfiles = JSON.parse(transactionRoot.dataset.walletProfiles || '[]');

    let prepared = null;

    function setTransactionStatus(message) {
        statusOutput.textContent = message;
    }

    function ethToWei(value) {
        const match = /^([0-9]+)(?:\.([0-9]{1,18}))?$/.exec(value.trim());
        if (!match) throw new Error('Enter a valid ETH amount with no more than 18 decimal places.');

        const whole = match[1];
        const fraction = (match[2] || '').padEnd(18, '0');
        return (BigInt(whole) * 1000000000000000000n + BigInt(fraction || '0')).toString();
    }

    function weiToDisplay(wei) {
        const divisor = 1000000000000000000n;
        const value = BigInt(wei);
        const whole = value / divisor;
        const fraction = (value % divisor).toString().padStart(18, '0').replace(/0+$/, '').slice(0, 6);
        return fraction ? `${whole}.${fraction} ETH` : `${whole} ETH`;
    }

    async function persistTransaction(payload) {
        const response = await fetch(transactionRoot.dataset.recordUrl, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify(payload),
        });

        if (!response.ok) {
            const body = await response.json().catch(() => ({}));
            throw new Error(body.message || 'The submitted transaction could not be recorded.');
        }

        return response.json();
    }

    reviewButton?.addEventListener('click', async () => {
        const injected = window.ethereum;
        if (!injected) {
            setTransactionStatus('No compatible browser wallet was detected.');
            return;
        }

        try {
            const recipient = recipientInput.value.trim();
            if (!/^0x[a-fA-F0-9]{40}$/.test(recipient)) {
                throw new Error('Enter a valid 42-character Ethereum recipient address.');
            }

            const amountWei = ethToWei(amountInput.value);
            if (BigInt(amountWei) <= 0n) throw new Error('Amount must be greater than zero.');

            const hexChainId = await injected.request({ method: 'eth_chainId' });
            if (Number.parseInt(hexChainId, 16) !== 11155111) {
                throw new Error('Switch the browser wallet to Ethereum Sepolia before preparing a transaction.');
            }

            const accounts = await injected.request({ method: 'eth_accounts' });
            if (!accounts.length) throw new Error('Connect the browser wallet before preparing a transaction.');

            const from = accounts[0];
            const profile = walletProfiles.find((item) => item.address.toLowerCase() === from.toLowerCase());
            if (!profile) throw new Error('The connected wallet is not linked to this application account.');

            let gasLimit = null;
            let gasPrice = null;
            let estimatedFeeText = 'Unavailable';

            try {
                gasLimit = await injected.request({
                    method: 'eth_estimateGas',
                    params: [{ from, to: recipient, value: `0x${BigInt(amountWei).toString(16)}` }],
                });
                gasPrice = await injected.request({ method: 'eth_gasPrice' });
                const fee = BigInt(gasLimit) * BigInt(gasPrice);
                estimatedFeeText = weiToDisplay(fee.toString());
            } catch {
                estimatedFeeText = 'Unavailable';
            }

            const balanceHex = await injected.request({ method: 'eth_getBalance', params: [from, 'latest'] });
            const balance = BigInt(balanceHex);
            const totalRequired = BigInt(amountWei) + (gasLimit && gasPrice ? BigInt(gasLimit) * BigInt(gasPrice) : 0n);
            if (totalRequired > balance) {
                throw new Error('Insufficient Sepolia test ETH for the amount and estimated network fee.');
            }

            prepared = {
                wallet_profile_id: profile.id,
                from_address: from,
                to_address: recipient,
                amount_wei: amountWei,
                chain_id: 11155111,
                gas_limit: gasLimit ? BigInt(gasLimit).toString() : null,
                gas_price_wei: gasPrice ? BigInt(gasPrice).toString() : null,
            };

            transactionRoot.querySelector('#review-from').textContent = from;
            transactionRoot.querySelector('#review-to').textContent = recipient;
            transactionRoot.querySelector('#review-amount').textContent = `${weiToDisplay(amountWei)} / ${amountWei} wei`;
            transactionRoot.querySelector('#review-fee').textContent = estimatedFeeText;
            reviewPanel.hidden = false;
            resultPanel.hidden = true;
            setTransactionStatus('Transaction details reviewed. Awaiting wallet approval.');
        } catch (error) {
            prepared = null;
            reviewPanel.hidden = true;
            setTransactionStatus(error.message || 'Unable to prepare the transaction.');
        }
    });

    approveButton?.addEventListener('click', async () => {
        if (!prepared) return;

        const injected = window.ethereum;
        if (!injected) {
            setTransactionStatus('Browser wallet is no longer available.');
            return;
        }

        try {
            setTransactionStatus('Confirm or reject the transaction in your browser wallet.');

            const transactionParameters = {
                from: prepared.from_address,
                to: prepared.to_address,
                value: `0x${BigInt(prepared.amount_wei).toString(16)}`,
            };
            if (prepared.gas_limit) transactionParameters.gas = `0x${BigInt(prepared.gas_limit).toString(16)}`;

            const transactionHash = await injected.request({
                method: 'eth_sendTransaction',
                params: [transactionParameters],
            });

            if (!/^0x[a-fA-F0-9]{64}$/.test(transactionHash)) {
                throw new Error('The wallet returned an invalid transaction hash.');
            }

            const result = await persistTransaction({
                ...prepared,
                transaction_hash: transactionHash,
            });

            resultPanel.hidden = false;
            resultStatus.textContent = `Submitted. Transaction hash: ${result.transaction_hash}. Confirmation is pending.`;
            explorerLink.href = `https://sepolia.etherscan.io/tx/${result.transaction_hash}`;
            explorerLink.hidden = false;
            setTransactionStatus('Transaction submitted. It is not confirmed until receipt verification.');

            let pollAttempts = 0;
            const pollStatus = async () => {
                pollAttempts += 1;

                try {
                    const hexChainId = await injected.request({ method: 'eth_chainId' });
                    if (Number.parseInt(hexChainId, 16) !== 11155111) {
                        await persistReconciledStatus(result.transaction_id, 'unknown');
                        resultStatus.textContent = 'Network changed. Status is unknown until Sepolia is selected again.';
                        return;
                    }

                    const receipt = await injected.request({
                        method: 'eth_getTransactionReceipt',
                        params: [result.transaction_hash],
                    });

                    if (!receipt) {
                        const transaction = await injected.request({
                            method: 'eth_getTransactionByHash',
                            params: [result.transaction_hash],
                        });
                        await persistReconciledStatus(result.transaction_id, transaction ? 'pending' : 'unknown');
                        resultStatus.textContent = transaction
                            ? 'Pending network confirmation.'
                            : 'Status unknown; the transaction could not be found or verified yet.';
                    } else {
                        const status = BigInt(receipt.status) === 1n ? 'confirmed' : 'failed';
                        await persistReconciledStatus(
                            result.transaction_id,
                            status,
                            BigInt(receipt.blockNumber).toString(),
                            BigInt(receipt.gasUsed).toString(),
                        );
                        resultStatus.textContent = status === 'confirmed'
                            ? 'Confirmed on Ethereum Sepolia.'
                            : 'Transaction failed or reverted on Ethereum Sepolia.';
                        if (status === 'confirmed' || status === 'failed') return;
                    }
                } catch (error) {
                    await persistReconciledStatus(result.transaction_id, 'unknown').catch(() => {});
                    resultStatus.textContent = 'Unable to verify transaction status. The state remains unknown.';
                }

                if (pollAttempts < 30) window.setTimeout(pollStatus, 10000);
            };

            const persistReconciledStatus = async (transactionId, status, blockNumber = null, gasUsed = null) => {
                await fetch(`/transactions/${transactionId}/reconcile`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({
                        status,
                        chain_id: 11155111,
                        block_number: blockNumber,
                        gas_used: gasUsed,
                    }),
                });
            };

            window.setTimeout(pollStatus, 5000);
        } catch (error) {
            if (error?.code === 4001) {
                setTransactionStatus('Transaction approval was rejected. No transaction was submitted.');
            } else {
                setTransactionStatus(error.message || 'Transaction submission failed.');
            }
        }
    });
}
