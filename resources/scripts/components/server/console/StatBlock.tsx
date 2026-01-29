import React from 'react';
import Icon from '@/components/elements/Icon';
import { IconDefinition } from '@fortawesome/free-solid-svg-icons';
import CopyOnClick from '@/components/elements/CopyOnClick';
import styled from 'styled-components';
import tw from 'twin.macro';

interface StatBlockProps {
    title: string;
    copyOnClick?: string;
    color?: string | undefined;
    icon: IconDefinition;
    children: React.ReactNode;
    className?: string;
}

const GlassCard = styled.div<{ $hasWarning?: boolean }>`
    ${tw`relative rounded-2xl p-5 transition-all duration-300`}
    background: rgba(30, 20, 50, 0.6);
    backdrop-filter: blur(20px);
    border: 1px solid ${props => props.$hasWarning ? 'rgba(255, 71, 87, 0.4)' : 'rgba(157, 78, 221, 0.3)'};
    box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37),
                0 0 20px ${props => props.$hasWarning ? 'rgba(255, 71, 87, 0.2)' : 'rgba(157, 78, 221, 0.15)'};
    
    &:hover {
        transform: translateY(-2px);
        border-color: ${props => props.$hasWarning ? 'rgba(255, 71, 87, 0.6)' : 'rgba(157, 78, 221, 0.5)'};
        box-shadow: 0 12px 40px 0 rgba(0, 0, 0, 0.5),
                    0 0 30px ${props => props.$hasWarning ? 'rgba(255, 71, 87, 0.3)' : 'rgba(157, 78, 221, 0.25)'};
    }
`;

const IconContainer = styled.div<{ $hasWarning?: boolean }>`
    ${tw`w-14 h-14 rounded-xl flex items-center justify-center mb-4`}
    background: ${props => props.$hasWarning
        ? 'linear-gradient(135deg, #ff4757 0%, #ff6b81 100%)'
        : 'linear-gradient(135deg, #9d4edd 0%, #00f5ff 100%)'};
    box-shadow: 0 4px 15px ${props => props.$hasWarning ? 'rgba(255, 71, 87, 0.4)' : 'rgba(157, 78, 221, 0.4)'};
`;

const StatLabel = styled.p`
    ${tw`text-xs uppercase tracking-widest mb-2`}
    color: rgba(255, 255, 255, 0.5);
    font-weight: 600;
    letter-spacing: 1.5px;
`;

const StatValue = styled.div`
    ${tw`text-2xl font-bold`}
    color: #ffffff;
    text-shadow: 0 0 10px rgba(157, 78, 221, 0.3);
`;

export default ({ title, copyOnClick, icon, color, className, children }: StatBlockProps) => {
    const hasWarning = color && (color.includes('red') || color.includes('yellow'));

    return (
        <CopyOnClick text={copyOnClick}>
            <GlassCard $hasWarning={hasWarning} className={className}>
                {/* Glowing Icon */}
                <IconContainer $hasWarning={hasWarning}>
                    <Icon
                        icon={icon}
                        className={'text-white'}
                        style={{ fontSize: '24px' }}
                    />
                </IconContainer>

                {/* Label */}
                <StatLabel>{title}</StatLabel>

                {/* Value */}
                <StatValue>{children}</StatValue>
            </GlassCard>
        </CopyOnClick>
    );
};
