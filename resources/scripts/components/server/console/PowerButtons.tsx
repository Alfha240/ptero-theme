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

// Simple flat buttons - no gradients or glow
const PowerBtn = styled.button<{ $variant: 'start' | 'restart' | 'stop' }>`
    ${tw`px-5 py-2 rounded font-medium text-sm transition-colors duration-150`}
    background: ${props =>
        props.$variant === 'start' ? '#22c55e' :
            props.$variant === 'restart' ? '#6366f1' :
                '#ef4444'};
    color: white;
    border: none;
    
    &:hover:not(:disabled) {
        background: ${props =>
        props.$variant === 'start' ? '#16a34a' :
            props.$variant === 'restart' ? '#4f46e5' :
                '#dc2626'};
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
                <PowerBtn
                    $variant="start"
                    disabled={status !== 'offline'}
                    onClick={onButtonClick.bind(this, 'start')}
                >
                    Start
                </PowerBtn>
            </Can>
            <Can action={'control.restart'}>
                <PowerBtn
                    $variant="restart"
                    disabled={!status}
                    onClick={onButtonClick.bind(this, 'restart')}
                >
                    Restart
                </PowerBtn>
            </Can>
            <Can action={'control.stop'}>
                <PowerBtn
                    $variant="stop"
                    disabled={status === 'offline'}
                    onClick={onButtonClick.bind(this, killable ? 'kill' : 'stop')}
                >
                    {killable ? 'Kill' : 'Stop'}
                </PowerBtn>
            </Can>
        </div>
    );
};
