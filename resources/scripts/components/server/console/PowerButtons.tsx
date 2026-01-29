import React, { useEffect, useState } from 'react';
import { Button } from '@/components/elements/button/index';
import Can from '@/components/elements/Can';
import { ServerContext } from '@/state/server';
import { PowerAction } from '@/components/server/console/ServerConsoleContainer';
import { Dialog } from '@/components/elements/dialog';
import styled from 'styled-components';
import tw from 'twin.macro';

interface PowerButtonProps {
    className?: string;
}

const GlowButton = styled.button<{ $variant: 'start' | 'restart' | 'stop' }>`
    ${tw`px-6 py-2.5 rounded-full font-semibold text-sm transition-all duration-300`}
    background: ${props =>
        props.$variant === 'start' ? 'linear-gradient(135deg, #00ff88 0%, #00d4aa 100%)' :
            props.$variant === 'restart' ? 'linear-gradient(135deg, #00f5ff 0%, #0099ff 100%)' :
                'linear-gradient(135deg, #ff4757 0%, #ff6b81 100%)'};
    color: ${props => props.$variant === 'restart' ? '#ffffff' : '#000000'};
    border: none;
    box-shadow: 0 4px 15px ${props =>
        props.$variant === 'start' ? 'rgba(0, 255, 136, 0.4)' :
            props.$variant === 'restart' ? 'rgba(0, 245, 255, 0.4)' :
                'rgba(255, 71, 87, 0.4)'};
    
    &:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px ${props =>
        props.$variant === 'start' ? 'rgba(0, 255, 136, 0.6)' :
            props.$variant === 'restart' ? 'rgba(0, 245, 255, 0.6)' :
                'rgba(255, 71, 87, 0.6)'};
    }
    
    &:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
`;

export default ({ className }: PowerButtonProps) => {
    const [open, setOpen] = useState(false);
    const status = ServerContext.useStoreState((state) => state.status.value);
    const instance = ServerContext.useStoreState((state) => state.socket.instance);

    const killable = status === 'stopping';
    const onButtonClick = (
        action: PowerAction | 'kill-confirmed',
        e: React.MouseEvent<HTMLButtonElement, MouseEvent>
    ): void => {
        e.preventDefault();
        if (action === 'kill') {
            return setOpen(true);
        }

        if (instance) {
            setOpen(false);
            instance.send('set state', action === 'kill-confirmed' ? 'kill' : action);
        }
    };

    useEffect(() => {
        if (status === 'offline') {
            setOpen(false);
        }
    }, [status]);

    return (
        <div className={className}>
            <Dialog.Confirm
                open={open}
                hideCloseIcon
                onClose={() => setOpen(false)}
                title={'Forcibly Stop Process'}
                confirm={'Continue'}
                onConfirmed={onButtonClick.bind(this, 'kill-confirmed')}
            >
                Forcibly stopping a server can lead to data corruption.
            </Dialog.Confirm>
            <Can action={'control.start'}>
                <GlowButton
                    $variant="start"
                    disabled={status !== 'offline'}
                    onClick={onButtonClick.bind(this, 'start')}
                >
                    Start
                </GlowButton>
            </Can>
            <Can action={'control.restart'}>
                <GlowButton
                    $variant="restart"
                    disabled={!status}
                    onClick={onButtonClick.bind(this, 'restart')}
                >
                    Restart
                </GlowButton>
            </Can>
            <Can action={'control.stop'}>
                <GlowButton
                    $variant="stop"
                    disabled={status === 'offline'}
                    onClick={onButtonClick.bind(this, killable ? 'kill' : 'stop')}
                >
                    {killable ? 'Kill' : 'Stop'}
                </GlowButton>
            </Can>
        </div>
    );
};
