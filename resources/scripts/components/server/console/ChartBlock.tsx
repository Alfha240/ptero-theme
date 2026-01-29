import React from 'react';
import styled from 'styled-components';
import tw from 'twin.macro';

interface ChartBlockProps {
    title: string;
    legend?: React.ReactNode;
    children: React.ReactNode;
}

const ChartContainer = styled.div`
    ${tw`rounded-xl overflow-hidden`}
    background: rgba(10, 15, 35, 0.9);
    border: 1px solid rgba(64, 128, 255, 0.15);
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.4);
`;

const ChartHeader = styled.div`
    ${tw`flex items-center justify-between px-4 py-3`}
    border-bottom: 1px solid rgba(64, 128, 255, 0.1);
`;

const ChartTitle = styled.h3`
    ${tw`font-medium text-sm`}
    color: rgba(255, 255, 255, 0.9);
    text-transform: uppercase;
    letter-spacing: 0.5px;
`;

const ChartContent = styled.div`
    ${tw`p-4`}
`;

export default ({ title, legend, children }: ChartBlockProps) => (
    <ChartContainer>
        <ChartHeader>
            <ChartTitle>{title}</ChartTitle>
            {legend && <div className={'text-sm flex items-center'}>{legend}</div>}
        </ChartHeader>
        <ChartContent>{children}</ChartContent>
    </ChartContainer>
);
