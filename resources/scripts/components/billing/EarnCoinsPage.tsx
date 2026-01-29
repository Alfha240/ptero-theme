import React, { useEffect, useState } from 'react';
import styled from 'styled-components';
import { getWallet, getBillingSettings, claimDailyReward, earnFromAd, redeemCode, WalletStats, BillingSettings } from '@/api/billing';
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

const BackLink = styled(Link)`
    color: rgba(255, 255, 255, 0.6);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1rem;
    
    &:hover {
        color: #fff;
    }
`;

const BalanceCard = styled.div`
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
    border-radius: 16px;
    padding: 2rem;
    margin-bottom: 2rem;
    text-align: center;
`;

const BalanceLabel = styled.div`
    color: rgba(255, 255, 255, 0.8);
    margin-bottom: 0.5rem;
`;

const BalanceValue = styled.div`
    font-size: 3rem;
    font-weight: 700;
    color: #fff;
`;

const Grid = styled.div`
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
`;

const EarnCard = styled.div`
    background: rgba(30, 35, 45, 0.8);
    border-radius: 12px;
    padding: 1.5rem;
    border: 1px solid rgba(255, 255, 255, 0.1);
`;

const CardIcon = styled.div`
    font-size: 2.5rem;
    margin-bottom: 1rem;
`;

const CardTitle = styled.h3`
    font-size: 1.25rem;
    font-weight: 600;
    color: #fff;
    margin-bottom: 0.5rem;
`;

const CardDescription = styled.p`
    color: rgba(255, 255, 255, 0.6);
    margin-bottom: 1rem;
    font-size: 0.875rem;
`;

const CardReward = styled.div`
    color: #fbbf24;
    font-weight: 600;
    margin-bottom: 1rem;
`;

const EarnButton = styled.button<{ disabled?: boolean }>`
    width: 100%;
    padding: 0.75rem 1.5rem;
    background: ${props => props.disabled ? 'rgba(255, 255, 255, 0.1)' : '#4f46e5'};
    color: ${props => props.disabled ? 'rgba(255, 255, 255, 0.4)' : 'white'};
    border: none;
    border-radius: 8px;
    font-weight: 500;
    cursor: ${props => props.disabled ? 'not-allowed' : 'pointer'};
    transition: background 0.2s;

    &:hover:not(:disabled) {
        background: #4338ca;
    }
`;

const CodeInput = styled.input`
    width: 100%;
    padding: 0.75rem 1rem;
    background: rgba(0, 0, 0, 0.3);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    color: #fff;
    margin-bottom: 1rem;
    text-transform: uppercase;

    &:focus {
        outline: none;
        border-color: #4f46e5;
    }
`;

const Message = styled.div<{ type: 'success' | 'error' }>`
    padding: 0.75rem 1rem;
    border-radius: 8px;
    margin-top: 1rem;
    background: ${props => props.type === 'success' ? 'rgba(34, 197, 94, 0.2)' : 'rgba(239, 68, 68, 0.2)'};
    color: ${props => props.type === 'success' ? '#22c55e' : '#ef4444'};
    font-size: 0.875rem;
`;

const Cooldown = styled.div`
    color: rgba(255, 255, 255, 0.5);
    font-size: 0.75rem;
    margin-top: 0.5rem;
`;

const EarnCoinsPage: React.FC = () => {
    const [stats, setStats] = useState<WalletStats | null>(null);
    const [settings, setSettings] = useState<BillingSettings | null>(null);
    const [loading, setLoading] = useState(true);
    const [code, setCode] = useState('');
    const [message, setMessage] = useState<{ text: string; type: 'success' | 'error' } | null>(null);
    const [adCooldown, setAdCooldown] = useState(0);
    const [dailyCooldown, setDailyCooldown] = useState(0);
    const [actionLoading, setActionLoading] = useState<string | null>(null);

    useEffect(() => {
        loadData();
    }, []);

    useEffect(() => {
        const interval = setInterval(() => {
            if (adCooldown > 0) setAdCooldown(c => c - 1);
            if (dailyCooldown > 0) setDailyCooldown(c => c - 1);
        }, 1000);
        return () => clearInterval(interval);
    }, [adCooldown, dailyCooldown]);

    const loadData = async () => {
        try {
            const [walletData, settingsData] = await Promise.all([
                getWallet(),
                getBillingSettings()
            ]);
            setStats(walletData);
            setSettings(settingsData);
            if (walletData.daily_cooldown) {
                setDailyCooldown(walletData.daily_cooldown);
            }
        } catch (error) {
            console.error('Failed to load data:', error);
        } finally {
            setLoading(false);
        }
    };

    const handleClaimDaily = async () => {
        setActionLoading('daily');
        setMessage(null);
        try {
            const result = await claimDailyReward();
            if (result.success) {
                setMessage({ text: `Claimed ${result.coins_earned} coins!`, type: 'success' });
                setStats(prev => prev ? { ...prev, coins: result.new_balance! } : null);
                setDailyCooldown(24 * 60 * 60);
            } else {
                setMessage({ text: result.message || 'Failed to claim', type: 'error' });
                if (result.next_claim_in) setDailyCooldown(result.next_claim_in);
            }
        } catch (error) {
            setMessage({ text: 'Failed to claim daily reward', type: 'error' });
        } finally {
            setActionLoading(null);
        }
    };

    const handleWatchAd = async () => {
        setActionLoading('ad');
        setMessage(null);
        try {
            const result = await earnFromAd();
            if (result.success) {
                setMessage({ text: `Earned ${result.coins_earned} coins!`, type: 'success' });
                setStats(prev => prev ? { ...prev, coins: result.new_balance! } : null);
                if (result.next_ad_in) setAdCooldown(result.next_ad_in);
            } else {
                setMessage({ text: result.message || 'Failed to earn', type: 'error' });
                if (result.cooldown_remaining) setAdCooldown(result.cooldown_remaining);
            }
        } catch (error) {
            setMessage({ text: 'Failed to process ad view', type: 'error' });
        } finally {
            setActionLoading(null);
        }
    };

    const handleRedeemCode = async () => {
        if (!code.trim()) return;
        setActionLoading('code');
        setMessage(null);
        try {
            const result = await redeemCode(code);
            if (result.success) {
                setMessage({ text: `Redeemed ${result.coins_earned} coins!`, type: 'success' });
                setStats(prev => prev ? { ...prev, coins: result.new_balance! } : null);
                setCode('');
            } else {
                setMessage({ text: result.message || 'Invalid code', type: 'error' });
            }
        } catch (error) {
            setMessage({ text: 'Failed to redeem code', type: 'error' });
        } finally {
            setActionLoading(null);
        }
    };

    const formatTime = (seconds: number) => {
        const h = Math.floor(seconds / 3600);
        const m = Math.floor((seconds % 3600) / 60);
        const s = seconds % 60;
        if (h > 0) return `${h}h ${m}m`;
        if (m > 0) return `${m}m ${s}s`;
        return `${s}s`;
    };

    if (loading) {
        return (
            <Container>
                <Spinner centered size="large" />
            </Container>
        );
    }

    return (
        <Container>
            <BackLink to="/billing">← Back to Wallet</BackLink>

            <Header>
                <Title>🎁 Earn Coins</Title>
            </Header>

            <BalanceCard>
                <BalanceLabel>Your Balance</BalanceLabel>
                <BalanceValue>{stats?.coins.toLocaleString() || 0} 🪙</BalanceValue>
            </BalanceCard>

            {message && <Message type={message.type}>{message.text}</Message>}

            <Grid>
                {/* Daily Reward */}
                <EarnCard>
                    <CardIcon>🎁</CardIcon>
                    <CardTitle>Daily Reward</CardTitle>
                    <CardDescription>Claim your free daily coins every 24 hours</CardDescription>
                    <CardReward>+{settings?.daily_reward || 50} coins</CardReward>
                    <EarnButton
                        onClick={handleClaimDaily}
                        disabled={dailyCooldown > 0 || actionLoading === 'daily'}
                    >
                        {actionLoading === 'daily' ? 'Claiming...' :
                            dailyCooldown > 0 ? `Wait ${formatTime(dailyCooldown)}` : 'Claim Now'}
                    </EarnButton>
                </EarnCard>

                {/* Watch Ads */}
                <EarnCard>
                    <CardIcon>📺</CardIcon>
                    <CardTitle>Watch Ads</CardTitle>
                    <CardDescription>Watch a short video to earn coins</CardDescription>
                    <CardReward>+{settings?.coins_per_ad || 10} coins</CardReward>
                    <EarnButton
                        onClick={handleWatchAd}
                        disabled={adCooldown > 0 || actionLoading === 'ad'}
                    >
                        {actionLoading === 'ad' ? 'Processing...' :
                            adCooldown > 0 ? `Wait ${formatTime(adCooldown)}` : 'Watch Ad'}
                    </EarnButton>
                    {settings && (
                        <Cooldown>Cooldown: {settings.ad_cooldown}s between ads</Cooldown>
                    )}
                </EarnCard>

                {/* Redeem Code */}
                <EarnCard>
                    <CardIcon>🎟️</CardIcon>
                    <CardTitle>Redeem Code</CardTitle>
                    <CardDescription>Enter a promo code to get free coins</CardDescription>
                    <CodeInput
                        type="text"
                        placeholder="Enter code..."
                        value={code}
                        onChange={(e) => setCode(e.target.value.toUpperCase())}
                        maxLength={50}
                    />
                    <EarnButton
                        onClick={handleRedeemCode}
                        disabled={!code.trim() || actionLoading === 'code'}
                    >
                        {actionLoading === 'code' ? 'Redeeming...' : 'Redeem Code'}
                    </EarnButton>
                </EarnCard>
            </Grid>
        </Container>
    );
};

export default EarnCoinsPage;
