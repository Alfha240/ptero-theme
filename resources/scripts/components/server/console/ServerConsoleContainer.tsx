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

const TopBar = styled.div`
    ${tw`rounded-2xl p-4 mb-4 flex items-center justify-between flex-wrap gap-4`}
    background: rgba(30, 20, 50, 0.8);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(157, 78, 221, 0.3);
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.3);
`;

const ServerTitleSection = styled.div`
    ${tw`flex items-center gap-4 flex-1`}
`;

const ServerName = styled.h1`
    ${tw`text-2xl font-bold`}
    color: #ffffff;
`;

const StatusBadge = styled.span<{ $isOnline?: boolean }>`
    ${tw`px-3 py-1 rounded-full text-xs font-semibold flex items-center gap-2`}
    background: ${props => props.$isOnline
        ? '#00ff88'
        : '#ff4757'};
    color: #000;
    
    &::before {
        content: '●';
        font-size: 10px;
    }
`;

const StatsBar = styled.div`
    ${tw`rounded-xl p-3 mb-4 flex items-center gap-6 flex-wrap`}
    background: rgba(20, 15, 35, 0.9);
    border: 1px solid rgba(157, 78, 221, 0.2);
`;

const StatItem = styled.div`
    ${tw`flex items-center gap-2`}
    color: rgba(255, 255, 255, 0.7);
    font-size: 14px;
    
    span {
        color: #ffffff;
        font-weight: 600;
    }
`;

const ConsoleWrapper = styled.div`
    ${tw`rounded-2xl mb-4 overflow-hidden`}
    background: rgba(0, 0, 0, 0.8);
    border: 1px solid rgba(0, 255, 136, 0.3);
    box-shadow: 0 0 30px rgba(0, 255, 136, 0.2);
`;

const ServerConsoleContainer = () => {
    const name = ServerContext.useStoreState((state) => state.server.data!.name);
    const description = ServerContext.useStoreState((state) => state.server.data!.description);
    const isInstalling = ServerContext.useStoreState((state) => state.server.isInstalling);
    const isTransferring = ServerContext.useStoreState((state) => state.server.data!.isTransferring);
    const eggFeatures = ServerContext.useStoreState((state) => state.server.data!.eggFeatures, isEqual);
    const isNodeUnderMaintenance = ServerContext.useStoreState((state) => state.server.data!.isNodeUnderMaintenance);
    const status = ServerContext.useStoreState((state) => state.status.value);
    const limits = ServerContext.useStoreState((state) => state.server.data!.limits);
    const primaryAllocation = ServerContext.useStoreState((state) => state.server.data!.allocations.find(a => a.isDefault));

    const isOnline = status === 'running';
    const allocation = `${primaryAllocation?.alias || primaryAllocation?.ip}:${primaryAllocation?.port}`;

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

                {/* Top Bar with Server Name, Status, and Power Buttons */}
                <TopBar>
                    <ServerTitleSection>
                        <ServerName>{name}</ServerName>
                        <StatusBadge $isOnline={isOnline}>
                            {isOnline ? 'Online' : 'Offline'}
                        </StatusBadge>
                    </ServerTitleSection>

                    {/* Power Buttons */}
                    <Can action={['control.start', 'control.stop', 'control.restart']} matchAny>
                        <PowerButtons className={'flex gap-3'} />
                    </Can>
                </TopBar>

                {/* Stats Bar Above Console - Like Image 2 */}
                <StatsBar>
                    <StatItem>
                        <span>📡 {allocation}</span>
                    </StatItem>
                    <StatItem>
                        <span>⚙️ {limits.cpu}%</span>
                    </StatItem>
                    <StatItem>
                        <span>💾 {limits.memory} MB</span>
                    </StatItem>
                    <StatItem>
                        <span>💿 {limits.disk} MB</span>
                    </StatItem>
                    <StatItem>
                        <span>🌐 Network</span>
                    </StatItem>
                </StatsBar>

                {/* Console - Full Width with Neon Glow */}
                <ConsoleWrapper>
                    <Spinner.Suspense>
                        <Console />
                    </Spinner.Suspense>
                </ConsoleWrapper>

                {/* Performance Graphs - Redesigned */}
                <div className={'grid grid-cols-1 md:grid-cols-3 gap-4 mb-4'}>
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
