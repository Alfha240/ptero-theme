import React from 'react';
import Icon from '@/components/elements/Icon';
import { IconDefinition } from '@fortawesome/free-solid-svg-icons';
import classNames from 'classnames';
import CopyOnClick from '@/components/elements/CopyOnClick';

interface StatBlockProps {
    title: string;
    copyOnClick?: string;
    color?: string | undefined;
    icon: IconDefinition;
    children: React.ReactNode;
    className?: string;
}

export default ({ title, copyOnClick, icon, color, className, children }: StatBlockProps) => {
    return (
        <CopyOnClick text={copyOnClick}>
            <div
                className={classNames(
                    'relative rounded-lg p-4 shadow-lg transition-all duration-200',
                    'bg-gray-800 hover:bg-gray-750 border border-gray-700',
                    'flex flex-col items-center justify-center text-center min-h-[120px]',
                    className
                )}
            >
                {/* Icon with colored background */}
                <div
                    className={classNames(
                        'w-12 h-12 rounded-lg flex items-center justify-center mb-3 shadow-md',
                        color && color.includes('red') ? 'bg-red-500' :
                            color && color.includes('yellow') ? 'bg-yellow-500' :
                                'bg-gradient-to-br from-purple-500 to-blue-500'
                    )}
                >
                    <Icon
                        icon={icon}
                        className={'text-white w-6 h-6'}
                    />
                </div>

                {/* Title */}
                <p className={'font-medium text-xs text-gray-400 uppercase tracking-wider mb-1'}>
                    {title}
                </p>

                {/* Value */}
                <div className={'text-lg font-bold text-white'}>
                    {children}
                </div>
            </div>
        </CopyOnClick>
    );
};
