import http from '@/api/http';

export interface WalletStats {
    coins: number;
    total_earned: number;
    total_spent: number;
    servers_count: number;
    max_servers: number;
    can_claim_daily: boolean;
    daily_cooldown: number | null;
}

export interface Transaction {
    id: number;
    type: 'credit' | 'debit';
    amount: number;
    formatted_amount: string;
    balance_after: number;
    source: string;
    icon: string;
    description: string;
    created_at: string;
}

export interface BillingPlan {
    id: number;
    name: string;
    description: string;
    memory: number;
    memory_formatted: string;
    disk: number;
    disk_formatted: string;
    cpu: number;
    cpu_formatted: string;
    databases: number;
    backups: number;
    allocations: number;
    coins_per_minute: number;
    hourly_cost: number;
    daily_cost: number;
    creation_cost: number;
}

export interface BillingSettings {
    coins_per_ad: number;
    ad_cooldown: number;
    daily_reward: number;
    max_servers: number;
    new_user_bonus: number;
}

export interface EarnResult {
    success: boolean;
    message?: string;
    coins_earned?: number;
    new_balance?: number;
    cooldown_remaining?: number;
    next_claim_in?: number;
    next_ad_in?: number;
}

// Get wallet stats
export const getWallet = async (): Promise<WalletStats> => {
    const { data } = await http.get('/api/client/billing/wallet');
    return data.data;
};

// Get transaction history
export const getTransactions = async (limit = 20): Promise<Transaction[]> => {
    const { data } = await http.get(`/api/client/billing/transactions?limit=${limit}`);
    return data.data;
};

// Get available plans
export const getPlans = async (): Promise<BillingPlan[]> => {
    const { data } = await http.get('/api/client/billing/plans');
    return data.data;
};

// Get billing settings
export const getBillingSettings = async (): Promise<BillingSettings> => {
    const { data } = await http.get('/api/client/billing/settings');
    return data.data;
};

// Claim daily reward
export const claimDailyReward = async (): Promise<EarnResult> => {
    const { data } = await http.post('/api/client/billing/earn/daily');
    return data;
};

// Earn from ad
export const earnFromAd = async (): Promise<EarnResult> => {
    const { data } = await http.post('/api/client/billing/earn/ad');
    return data;
};

// Redeem code
export const redeemCode = async (code: string): Promise<EarnResult> => {
    const { data } = await http.post('/api/client/billing/redeem', { code });
    return data;
};

// Check if can create server
export const checkServerCreation = async (planId: number): Promise<{
    can_create: boolean;
    reason?: string;
    creation_cost?: number;
    hourly_cost?: number;
}> => {
    const { data } = await http.post('/api/client/billing/check-server', { plan_id: planId });
    return data.data;
};
