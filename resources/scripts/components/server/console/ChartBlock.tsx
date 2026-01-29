import React from 'react';
import styled from 'styled-components';
import tw from 'twin.macro';

interface ChartBlockProps {
    title: string;
    legend?: React.ReactNode;
    children: React.ReactNode;
}

// Simple dark card - no fancy borders or shadows
const ChartContainer = styled.div`
    ${tw`rounded overflow-hidden bg-gray-800`}
`;

const ChartHeader = styled.div`
    ${tw`flex items-center justify-between px-4 py-3 border-b border-gray-700`}
`;

const ChartTitle = styled.h3`
    ${tw`font-medium text-sm text-gray-300 uppercase tracking-wide`}
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
