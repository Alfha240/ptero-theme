import React, { useEffect, useState } from 'react';
import styled from 'styled-components';
import { getWallet, getPlans, checkServerCreation, WalletStats, BillingPlan } from '@/api/billing';
import Spinner from '@/components/elements/Spinner';
import { Link } from 'react-router-dom';
import { useHistory } from 'react-router';


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

const BalanceBar = styled.div`
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: rgba(30, 35, 45, 0.8);
    border-radius: 12px;
    padding: 1rem 1.5rem;
    margin-bottom: 2rem;
    border: 1px solid rgba(255, 255, 255, 0.1);
`;

const Balance = styled.div`
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 1.25rem;
    font-weight: 600;
    color: #fbbf24;
`;

const EarnLink = styled(Link)`
    color: #4f46e5;
    text-decoration: none;
    font-weight: 500;
    
    &:hover {
        text-decoration: underline;
    }
`;

const Grid = styled.div`
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
`;

const PlanCard = styled.div<{ popular?: boolean }>`
    background: rgba(30, 35, 45, 0.8);
    border-radius: 16px;
    padding: 2rem;
    border: 2px solid ${props => props.popular ? '#4f46e5' : 'rgba(255, 255, 255, 0.1)'};
    position: relative;
    transition: transform 0.2s, border-color 0.2s;

    &:hover {
        transform: translateY(-4px);
        border-color: #4f46e5;
    }
`;

const PopularBadge = styled.div`
    position: absolute;
    top: -12px;
    left: 50%;
    transform: translateX(-50%);
    background: #4f46e5;
    color: white;
    padding: 0.25rem 1rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
`;

const PlanName = styled.h3`
    font-size: 1.5rem;
    font-weight: 700;
    color: #fff;
    margin-bottom: 0.5rem;
    text-align: center;
`;

const PlanDescription = styled.p`
    color: rgba(255, 255, 255, 0.6);
    text-align: center;
    margin-bottom: 1.5rem;
    font-size: 0.875rem;
`;

const SpecsList = styled.div`
    margin-bottom: 1.5rem;
`;

const SpecItem = styled.div`
    display: flex;
    justify-content: space-between;
    padding: 0.5rem 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    
    &:last-child {
        border-bottom: none;
    }
`;

const SpecLabel = styled.span`
    color: rgba(255, 255, 255, 0.6);
`;

const SpecValue = styled.span`
    color: #fff;
    font-weight: 500;
`;

const PriceSection = styled.div`
    text-align: center;
    padding: 1rem 0;
    margin-bottom: 1rem;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
`;

const PriceLabel = styled.div`
    font-size: 0.75rem;
    color: rgba(255, 255, 255, 0.5);
    margin-bottom: 0.25rem;
`;

const PriceValue = styled.div`
    font-size: 1.75rem;
    font-weight: 700;
    color: #fbbf24;
`;

const PriceUnit = styled.span`
    font-size: 0.875rem;
    color: rgba(255, 255, 255, 0.6);
`;

const CostInfo = styled.div`
    display: flex;
    justify-content: space-between;
    font-size: 0.75rem;
    color: rgba(255, 255, 255, 0.5);
    margin-bottom: 1rem;
`;

const SelectButton = styled.button<{ disabled?: boolean }>`
    width: 100%;
    padding: 1rem;
    background: ${props => props.disabled ? 'rgba(255, 255, 255, 0.1)' : '#4f46e5'};
    color: ${props => props.disabled ? 'rgba(255, 255, 255, 0.4)' : 'white'};
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: ${props => props.disabled ? 'not-allowed' : 'pointer'};
    transition: background 0.2s;

    &:hover:not(:disabled) {
        background: #4338ca;
    }
`;

const Message = styled.div<{ type: 'error' | 'info' }>`
    padding: 0.75rem 1rem;
    border-radius: 8px;
    margin-top: 0.75rem;
    background: ${props => props.type === 'error' ? 'rgba(239, 68, 68, 0.2)' : 'rgba(79, 70, 229, 0.2)'};
    color: ${props => props.type === 'error' ? '#ef4444' : '#818cf8'};
    font-size: 0.75rem;
    text-align: center;
`;

const PlansPage: React.FC = () => {
    const [stats, setStats] = useState<WalletStats | null>(null);
    const [plans, setPlans] = useState<BillingPlan[]>([]);
    const [loading, setLoading] = useState(true);
    const [selectedPlan, setSelectedPlan] = useState<number | null>(null);
    const [checkResult, setCheckResult] = useState<{ [key: number]: { can: boolean; reason?: string } }>({});
    const history = useHistory();


    useEffect(() => {
        loadData();
    }, []);

    const loadData = async () => {
        try {
            const [walletData, plansData] = await Promise.all([
                getWallet(),
                getPlans()
            ]);
            setStats(walletData);
            setPlans(plansData);
        } catch (error) {
            console.error('Failed to load data:', error);
        } finally {
            setLoading(false);
        }
    };

    const handleSelectPlan = async (plan: BillingPlan) => {
        setSelectedPlan(plan.id);
        try {
            const result = await checkServerCreation(plan.id);
            setCheckResult(prev => ({
                ...prev,
                [plan.id]: { can: result.can_create, reason: result.reason }
            }));

            if (result.can_create) {
                // TODO: Navigate to server creation with plan
                // For now, just show success
                alert(`Plan "${plan.name}" selected! Server creation would start here.`);
            }
        } catch (error) {
            setCheckResult(prev => ({
                ...prev,
                [plan.id]: { can: false, reason: 'Failed to check eligibility' }
            }));
        } finally {
            setSelectedPlan(null);
        }
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
                <Title>📦 Server Plans</Title>
            </Header>

            <BalanceBar>
                <Balance>🪙 {stats?.coins.toLocaleString() || 0} coins</Balance>
                <EarnLink to="/billing/earn">Need more coins?</EarnLink>
            </BalanceBar>

            <Grid>
                {plans.map((plan, index) => (
                    <PlanCard key={plan.id} popular={index === 1}>
                        {index === 1 && <PopularBadge>POPULAR</PopularBadge>}
                        <PlanName>{plan.name}</PlanName>
                        <PlanDescription>{plan.description}</PlanDescription>

                        <SpecsList>
                            <SpecItem>
                                <SpecLabel>RAM</SpecLabel>
                                <SpecValue>{plan.memory_formatted}</SpecValue>
                            </SpecItem>
                            <SpecItem>
                                <SpecLabel>Disk</SpecLabel>
                                <SpecValue>{plan.disk_formatted}</SpecValue>
                            </SpecItem>
                            <SpecItem>
                                <SpecLabel>CPU</SpecLabel>
                                <SpecValue>{plan.cpu_formatted}</SpecValue>
                            </SpecItem>
                            <SpecItem>
                                <SpecLabel>Databases</SpecLabel>
                                <SpecValue>{plan.databases}</SpecValue>
                            </SpecItem>
                            <SpecItem>
                                <SpecLabel>Backups</SpecLabel>
                                <SpecValue>{plan.backups}</SpecValue>
                            </SpecItem>
                        </SpecsList>

                        <PriceSection>
                            <PriceLabel>Cost per minute</PriceLabel>
                            <PriceValue>
                                {plan.coins_per_minute} <PriceUnit>coins/min</PriceUnit>
                            </PriceValue>
                        </PriceSection>

                        <CostInfo>
                            <span>Hourly: {plan.hourly_cost} coins</span>
                            <span>Daily: {plan.daily_cost} coins</span>
                        </CostInfo>

                        {plan.creation_cost > 0 && (
                            <CostInfo>
                                <span>Creation fee: {plan.creation_cost} coins</span>
                            </CostInfo>
                        )}

                        <SelectButton
                            onClick={() => handleSelectPlan(plan)}
                            disabled={selectedPlan === plan.id}
                        >
                            {selectedPlan === plan.id ? 'Checking...' : 'Create Server'}
                        </SelectButton>

                        {checkResult[plan.id] && !checkResult[plan.id].can && (
                            <Message type="error">{checkResult[plan.id].reason}</Message>
                        )}
                    </PlanCard>
                ))}
            </Grid>
        </Container>
    );
};

export default PlansPage;
