import React, { useEffect, useState } from 'react';
import styled from 'styled-components';
import { getWallet, getTransactions, WalletStats, Transaction } from '@/api/billing';
import Spinner from '@/components/elements/Spinner';
import { Link } from 'react-router-dom';

const Container = styled.div`
    padding: 2rem;
    max-width: 1200px;
    margin: 0 auto;
`;

const Header = styled.div`
    margin-bottom: 2rem;
`;

const Title = styled.h1`
    font-size: 1.75rem;
    font-weight: 600;
    color: #fff;
    margin-bottom: 0.5rem;
`;

const Subtitle = styled.p`
    color: rgba(255, 255, 255, 0.6);
`;

const Grid = styled.div`
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
`;

const StatCard = styled.div`
    background: rgba(30, 35, 45, 0.8);
    border-radius: 12px;
    padding: 1.5rem;
    border: 1px solid rgba(255, 255, 255, 0.1);
`;

const StatLabel = styled.div`
    font-size: 0.875rem;
    color: rgba(255, 255, 255, 0.5);
    margin-bottom: 0.5rem;
`;

const StatValue = styled.div`
    font-size: 1.75rem;
    font-weight: 700;
    color: #fff;
`;

const CoinsValue = styled(StatValue)`
    color: #fbbf24;
`;

const ActionButtons = styled.div`
    display: flex;
    gap: 1rem;
    margin-bottom: 2rem;
    flex-wrap: wrap;
`;

const ActionButton = styled(Link)`
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    background: #4f46e5;
    color: white;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 500;
    transition: background 0.2s;

    &:hover {
        background: #4338ca;
    }
`;

const SecondaryButton = styled(ActionButton)`
    background: rgba(255, 255, 255, 0.1);
    
    &:hover {
        background: rgba(255, 255, 255, 0.15);
    }
`;

const TransactionList = styled.div`
    background: rgba(30, 35, 45, 0.8);
    border-radius: 12px;
    border: 1px solid rgba(255, 255, 255, 0.1);
    overflow: hidden;
`;

const TransactionHeader = styled.div`
    padding: 1rem 1.5rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    font-weight: 600;
    color: #fff;
`;

const TransactionItem = styled.div`
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem 1.5rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);

    &:last-child {
        border-bottom: none;
    }
`;

const TransactionLeft = styled.div`
    display: flex;
    align-items: center;
    gap: 0.75rem;
`;

const TransactionIcon = styled.span`
    font-size: 1.25rem;
`;

const TransactionInfo = styled.div``;

const TransactionSource = styled.div`
    font-weight: 500;
    color: #fff;
    text-transform: capitalize;
`;

const TransactionDate = styled.div`
    font-size: 0.75rem;
    color: rgba(255, 255, 255, 0.4);
`;

const TransactionAmount = styled.div<{ type: 'credit' | 'debit' }>`
    font-weight: 600;
    color: ${props => props.type === 'credit' ? '#22c55e' : '#ef4444'};
`;

const EmptyState = styled.div`
    padding: 3rem;
    text-align: center;
    color: rgba(255, 255, 255, 0.5);
`;

const WalletPage: React.FC = () => {
    const [stats, setStats] = useState<WalletStats | null>(null);
    const [transactions, setTransactions] = useState<Transaction[]>([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        loadData();
    }, []);

    const loadData = async () => {
        try {
            const [walletData, txData] = await Promise.all([
                getWallet(),
                getTransactions(20)
            ]);
            setStats(walletData);
            setTransactions(txData);
        } catch (error) {
            console.error('Failed to load wallet data:', error);
        } finally {
            setLoading(false);
        }
    };

    if (loading) {
        return (
            <Container>
                <Spinner centered size="large" />
            </Container>
        );
    }

    if (!stats) {
        return (
            <Container>
                <EmptyState>Failed to load wallet data</EmptyState>
            </Container>
        );
    }

    return (
        <Container>
            <Header>
                <Title>💰 My Wallet</Title>
                <Subtitle>Manage your coins and view transaction history</Subtitle>
            </Header>

            <Grid>
                <StatCard>
                    <StatLabel>Current Balance</StatLabel>
                    <CoinsValue>{stats.coins.toLocaleString()} 🪙</CoinsValue>
                </StatCard>
                <StatCard>
                    <StatLabel>Total Earned</StatLabel>
                    <StatValue style={{ color: '#22c55e' }}>{stats.total_earned.toLocaleString()}</StatValue>
                </StatCard>
                <StatCard>
                    <StatLabel>Total Spent</StatLabel>
                    <StatValue style={{ color: '#ef4444' }}>{stats.total_spent.toLocaleString()}</StatValue>
                </StatCard>
                <StatCard>
                    <StatLabel>Servers</StatLabel>
                    <StatValue>{stats.servers_count} / {stats.max_servers}</StatValue>
                </StatCard>
            </Grid>

            <ActionButtons>
                <ActionButton to="/billing/earn">
                    🎁 Earn Coins
                </ActionButton>
                <SecondaryButton to="/billing/plans">
                    📦 View Plans
                </SecondaryButton>
            </ActionButtons>

            <TransactionList>
                <TransactionHeader>Recent Transactions</TransactionHeader>
                {transactions.length === 0 ? (
                    <EmptyState>No transactions yet</EmptyState>
                ) : (
                    transactions.map(tx => (
                        <TransactionItem key={tx.id}>
                            <TransactionLeft>
                                <TransactionIcon>{tx.icon}</TransactionIcon>
                                <TransactionInfo>
                                    <TransactionSource>{tx.source}</TransactionSource>
                                    <TransactionDate>
                                        {new Date(tx.created_at).toLocaleDateString()}
                                    </TransactionDate>
                                </TransactionInfo>
                            </TransactionLeft>
                            <TransactionAmount type={tx.type}>
                                {tx.formatted_amount}
                            </TransactionAmount>
                        </TransactionItem>
                    ))
                )}
            </TransactionList>
        </Container>
    );
};

export default WalletPage;
