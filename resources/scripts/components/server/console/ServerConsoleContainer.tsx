import React, { memo } from 'react';
import { ServerContext } from '@/state/server';
import Can from '@/components/elements/Can';
import ServerContentBlock from '@/components/elements/ServerContentBlock';
import isEqual from 'react-fast-compare';
import Spinner from '@/components/elements/Spinner';
import Features from '@feature/Features';
import Console from '@/components/server/console/Console';
import StatGraphs from '@/components/server/console/StatGraphs';
import PowerButtons from '@/components/server/console/PowerButtons';
import ServerDetailsBlock from '@/components/server/console/ServerDetailsBlock';
import { Alert } from '@/components/elements/alert';
import styled from 'styled-components';
import tw from 'twin.macro';

export type PowerAction = 'start' | 'stop' | 'restart' | 'kill';

const GradientBackground = styled.div`
    ${tw`min-h-screen p-6`}
    background: linear-gradient(135deg, #1a0b2e 0%, #16213e 100%);
`;

const GlassContainer = styled.div`
    ${tw`rounded-3xl p-6 mb-6`}
    background: rgba(255, 255, 255, 0.03);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
`;

const StatusBar = styled.div`
    ${tw`flex items-center justify-between mb-6 flex-wrap gap-4`}
`;

const ServerInfo = styled.div`
    ${tw`flex items-center gap-4`}
`;

const ServerNameconst = styled.h1`
    ${tw`text-3xl font-bold`}
    color: #ffffff;
    text-shadow: 0 0 20px rgba(157, 78, 221, 0.5);
`;

const StatusBadge = styled.span<{ $isOnline?: boolean }>`
    ${tw`px-4 py-2 rounded-full text-sm font-semibold flex items-center gap-2`}
    background: ${props => props.$isOnline
        ? 'linear-gradient(135deg, #00ff88 0%, #00d4aa 100%)'
        : 'linear-gradient(135deg, #ff4757 0%, #ff6b81 100%)'};
    color: #000;
    box-shadow: 0 4px 15px ${props => props.$isOnline
        ? 'rgba(0, 255, 136, 0.4)'
        : 'rgba(255, 71, 87, 0.4)'};
    
    &::before {
        content: '●';
        font-size: 12px;
    }
`;

const PlanBadge = styled.span`
    ${tw`px-4 py-2 rounded-full text-sm font-semibold`}
    background: rgba(157, 78, 221, 0.2);
    border: 1px solid rgba(157, 78, 221, 0.4);
    color: #9d4edd;
`;

const ConsoleWrapper = styled.div`
    ${tw`rounded-2xl mb-6 overflow-hidden`}
    background: rgba(0, 0, 0, 0.8);
    border: 1px solid rgba(0, 255, 136, 0.3);
    box-shadow: 0 0 30px rgba(0, 255, 136, 0.2),
                0 8px 32px rgba(0, 0, 0, 0.5);
`;

const StatsGrid = styled.div`
    ${tw`grid gap-4 mb-6`}
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    
    @media (min-width: 1024px) {
        grid-template-columns: repeat(4, 1fr);
    }
`;

const ServerConsoleContainer = () => {
    const name = ServerContext.useStoreState((state) => state.server.data!.name);
    const description = ServerContext.useStoreState((state) => state.server.data!.description);
    const isInstalling = ServerContext.useStoreState((state) => state.server.isInstalling);
    const isTransferring = ServerContext.useStoreState((state) => state.server.data!.isTransferring);
    const eggFeatures = ServerContext.useStoreState((state) => state.server.data!.eggFeatures, isEqual);
    const isNodeUnderMaintenance = ServerContext.useStoreState((state) => state.server.data!.isNodeUnderMaintenance);
    const status = ServerContext.useStoreState((state) => state.status.value);

    const isOnline = status === 'running';

    return (
        <GradientBackground>
            <ServerContentBlock title={''}>
                {(isNodeUnderMaintenance || isInstalling || isTransferring) && (
                    <Alert type={'warning'} className={'mb-4'}>
                        {isNodeUnderMaintenance
                            ? 'The node of this server is currently under maintenance and all actions are unavailable.'
                            : isInstalling
                                ? 'This server is currently running its installation process and most actions are unavailable.'
                                : 'This server is currently being transferred to another node and all actions are unavailable.'}
                    </Alert>
                )}

                <GlassContainer>
                    {/* Status Bar */}
                    <StatusBar>
                        <ServerInfo>
                            <ServerName>{name}</ServerName>
                            <StatusBadge $isOnline={isOnline}>
                                {isOnline ? 'Online' : 'Offline'}
                            </StatusBadge>
                            <PlanBadge>Premium Plan</PlanBadge>
                        </ServerInfo>

                        {/* Power Buttons */}
                        <Can action={['control.start', 'control.stop', 'control.restart']} matchAny>
                            <PowerButtons className={'flex gap-3'} />
                        </Can>
                    </StatusBar>

                    {/* Server Description */}
                    {description && (
                        <p className={'text-sm mb-4'} style={{ color: 'rgba(255, 255, 255, 0.6)' }}>
                            {description}
                        </p>
                    )}
                </GlassContainer>

                {/* Console - Full Width with Neon Glow */}
                <ConsoleWrapper>
                    <Spinner.Suspense>
                        <Console />
                    </Spinner.Suspense>
                </ConsoleWrapper>

                {/* Server Stats Cards - Glassmorphism Style */}
                <StatsGrid>
                    <ServerDetailsBlock className={''} />
                </StatsGrid>

                {/* Performance Graphs */}
                <div className={'grid grid-cols-1 md:grid-cols-3 gap-4'}>
                    <Spinner.Suspense>
                        <StatGraphs />
                    </Spinner.Suspense>
                </div>

                <Features enabled={eggFeatures} />
            </ServerContentBlock>
        </GradientBackground>
    );
};

export default memo(ServerConsoleContainer, isEqual);
