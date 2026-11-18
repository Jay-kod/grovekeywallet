

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
                return;
            }

            chainOutput.textContent = 'Ethereum Sepolia (11155111)';
            await persistWallet(address, chainId);
            setStatus('Connected to Ethereum Sepolia.', 'success');
            switchButton.hidden = true;
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
