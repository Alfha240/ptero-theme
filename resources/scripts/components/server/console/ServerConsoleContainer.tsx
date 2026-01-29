import React, { memo, useState } from 'react';
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
import {
    faTerminal,
    faServer,
    faChartLine,
    faBolt
} from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import tw from 'twin.macro';
import styled from 'styled-components';

export type PowerAction = 'start' | 'stop' | 'restart' | 'kill';

const SidebarWrapper = styled.div`
    ${tw`flex min-h-screen`}
    background: #1a1c2e;
    border-radius: 8px;
    overflow: hidden;
`;

const Sidebar = styled.div`
    ${tw`w-60 flex-shrink-0`}
    background: #252841;
    border-right: 1px solid #2d3152;
    padding: 1.5rem 0;
`;

const SidebarItem = styled.div<{ active: boolean }>`
    ${tw`px-6 py-4 cursor-pointer transition-all flex items-center gap-3`}
    color: ${props => props.active ? '#fff' : '#a8b3cf'};
    background: ${props => props.active ? 'rgba(99, 102, 241, 0.15)' : 'transparent'};
    border-left: 3px solid ${props => props.active ? '#6366f1' : 'transparent'};
    font-size: 14px;
    
    &:hover {
        background: rgba(99, 102, 241, 0.1);
        color: #fff;
    }
    
    svg {
        font-size: 18px;
        width: 20px;
        text-align: center;
    }
`;

const MainContent = styled.div`
    ${tw`flex-1 p-6 overflow-y-auto`}
    background: #1a1c2e;
    max-height: 100vh;
`;

const SectionTitle = styled.h2`
    ${tw`text-2xl font-semibold mb-6`}
    color: #fff;
    border-bottom: 2px solid #2d3152;
    padding-bottom: 0.75rem;
`;

const ServerConsoleContainer = () => {
    const [activeSection, setActiveSection] = useState<'console' | 'info' | 'performance' | 'power'>('console');

    const name = ServerContext.useStoreState((state) => state.server.data!.name);
    const description = ServerContext.useStoreState((state) => state.server.data!.description);
    const isInstalling = ServerContext.useStoreState((state) => state.server.isInstalling);
    const isTransferring = ServerContext.useStoreState((state) => state.server.data!.isTransferring);
    const eggFeatures = ServerContext.useStoreState((state) => state.server.data!.eggFeatures, isEqual);
    const isNodeUnderMaintenance = ServerContext.useStoreState((state) => state.server.data!.isNodeUnderMaintenance);

    return (
        <ServerContentBlock title={name}>
            {(isNodeUnderMaintenance || isInstalling || isTransferring) && (
                <Alert type={'warning'} className={'mb-4'}>
                    {isNodeUnderMaintenance
                        ? 'The node of this server is currently under maintenance and all actions are unavailable.'
                        : isInstalling
                            ? 'This server is currently running its installation process and most actions are unavailable.'
                            : 'This server is currently being transferred to another node and all actions are unavailable.'}
                </Alert>
            )}

            <SidebarWrapper>
                {/* Left Sidebar Navigation */}
                <Sidebar>
                    <SidebarItem
                        active={activeSection === 'console'}
                        onClick={() => setActiveSection('console')}
                    >
                        <FontAwesomeIcon icon={faTerminal} />
                        <span>Console</span>
                    </SidebarItem>

                    <SidebarItem
                        active={activeSection === 'info'}
                        onClick={() => setActiveSection('info')}
                    >
                        <FontAwesomeIcon icon={faServer} />
                        <span>Server Info</span>
                    </SidebarItem>

                    <SidebarItem
                        active={activeSection === 'performance'}
                        onClick={() => setActiveSection('performance')}
                    >
                        <FontAwesomeIcon icon={faChartLine} />
                        <span>Performance</span>
                    </SidebarItem>

                    <Can action={['control.start', 'control.stop', 'control.restart']} matchAny>
                        <SidebarItem
                            active={activeSection === 'power'}
                            onClick={() => setActiveSection('power')}
                        >
                            <FontAwesomeIcon icon={faBolt} />
                            <span>Power Control</span>
                        </SidebarItem>
                    </Can>
                </Sidebar>

                {/* Main Content Area */}
                <MainContent>
                    {/* Server Header */}
                    <div className={'mb-6'}>
                        <h1 className={'font-header font-medium text-2xl text-gray-50 leading-relaxed'}>
                            {name}
                        </h1>
                        <p className={'text-sm text-gray-400'}>{description}</p>
                    </div>

                    {/* Console Section */}
                    {activeSection === 'console' && (
                        <div>
                            <SectionTitle>Server Console</SectionTitle>
                            <Spinner.Suspense>
                                <Console />
                            </Spinner.Suspense>
                        </div>
                    )}

                    {/* Server Info Section */}
                    {activeSection === 'info' && (
                        <div>
                            <SectionTitle>Server Information</SectionTitle>
                            <ServerDetailsBlock className={'grid-cols-2 md:grid-cols-3 lg:grid-cols-4'} />
                        </div>
                    )}

                    {/* Performance Section */}
                    {activeSection === 'performance' && (
                        <div>
                            <SectionTitle>Performance Graphs</SectionTitle>
                            <div className={'grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-4'}>
                                <Spinner.Suspense>
                                    <StatGraphs />
                                </Spinner.Suspense>
                            </div>
                        </div>
                    )}

                    {/* Power Control Section */}
                    {activeSection === 'power' && (
                        <Can action={['control.start', 'control.stop', 'control.restart']} matchAny>
                            <div>
                                <SectionTitle>Power Control</SectionTitle>
                                <div className={'bg-gray-800 p-6 rounded-lg'}>
                                    <p className={'text-gray-300 mb-4'}>
                                        Control your server's power state using the buttons below.
                                    </p>
                                    <PowerButtons className={'flex gap-3'} />
                                </div>
                            </div>
                        </Can>
                    )}

                    {/* Features Section (shown on all pages) */}
                    <div className={'mt-8'}>
                        <Features enabled={eggFeatures} />
                    </div>
                </MainContent>
            </SidebarWrapper>
        </ServerContentBlock>
    );
};

export default memo(ServerConsoleContainer, isEqual);
