import styled from 'styled-components/macro';
import tw from 'twin.macro';

const SubNavigation = styled.div`
    position: fixed;
    left: 0;
    top: 60px;
    height: calc(100vh - 60px);
    width: 250px;
    background: rgba(20, 15, 35, 0.95);
    backdrop-filter: blur(20px);
    border-right: 1px solid rgba(157, 78, 221, 0.2);
    overflow-y: auto;
    z-index: 40;
    box-shadow: 4px 0 16px rgba(0, 0, 0, 0.3);

    & > div {
        display: flex;
        flex-direction: column;
        padding: 20px 12px;
        gap: 8px;

        & > a,
        & > div {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            border-radius: 8px;
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease;
            white-space: nowrap;

            &:hover {
                background: rgba(157, 78, 221, 0.15);
                color: #ffffff;
                transform: translateX(4px);
            }

            &:active,
            &.active {
                background: linear-gradient(90deg, rgba(157, 78, 221, 0.25) 0%, rgba(157, 78, 221, 0.1) 100%);
                color: #ffffff;
                border-left: 3px solid #9d4edd;
                box-shadow: 0 2px 8px rgba(157, 78, 221, 0.3);
            }
        }
    }

    /* Scrollbar Styling */
    &::-webkit-scrollbar {
        width: 6px;
    }

    &::-webkit-scrollbar-track {
        background: rgba(0, 0, 0, 0.2);
    }

    &::-webkit-scrollbar-thumb {
        background: rgba(157, 78, 221, 0.4);
        border-radius: 3px;

        &:hover {
            background: rgba(157, 78, 221, 0.6);
        }
    }

    /* Mobile Responsive */
    @media (max-width: 768px) {
        transform: translateX(-100%);
        transition: transform 0.3s ease;

        &.mobile-open {
            transform: translateX(0);
        }
    }
`;

export default SubNavigation;
