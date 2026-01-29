import React, { useEffect, useState } from 'react';
import styled from 'styled-components';
import { Link } from 'react-router-dom';
import { getWallet, WalletStats } from '@/api/billing';

const Container = styled(Link)`
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: rgba(251, 191, 36, 0.1);
    border: 1px solid rgba(251, 191, 36, 0.3);
    border-radius: 8px;
    text-decoration: none;
    transition: all 0.2s;

    &:hover {
        background: rgba(251, 191, 36, 0.15);
        border-color: rgba(251, 191, 36, 0.5);
    }
`;

const CoinIcon = styled.span`
    font-size: 1rem;
`;

const CoinAmount = styled.span`
    color: #fbbf24;
    font-weight: 600;
    font-size: 0.875rem;
`;

const CoinDisplay: React.FC = () => {
    const [coins, setCoins] = useState<number | null>(null);

    useEffect(() => {
        loadWallet();
    }, []);

    const loadWallet = async () => {
        try {
            const data = await getWallet();
            setCoins(data.coins);
        } catch (error) {
            console.error('Failed to load wallet:', error);
        }
    };

    if (coins === null) {
        return null;
    }

    return (
        <Container to="/billing">
            <CoinIcon>🪙</CoinIcon>
            <CoinAmount>{coins.toLocaleString()}</CoinAmount>
        </Container>
    );
};

export default CoinDisplay;
