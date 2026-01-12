import Styled from "vue3-styled-components";
const props = [
    "innerWidth",
    "collapsed",
    "cssSettings",
    "isRtl",
    "rightSidebarValue",
];

const Div = Styled("div", props)`

`;

const MainArea = Styled("div", props)`
	${({ innerWidth, collapsed, isRtl }) =>
        isRtl
            ? innerWidth <= 991
                ? "margin-right: 2px;"
                : collapsed
                    ? "margin-right: 80px;"
                    : "margin-right: 240px;"
            : ""}
	${({ innerWidth, collapsed, isRtl }) =>
        !isRtl
            ? innerWidth <= 991
                ? "margin-left: 2px;"
                : collapsed
                    ? "margin-left: 80px;"
                    : "margin-left: 240px;"
            : ""}

	//for right side bar//
	widht:${({ innerWidth, rightSidebarValue }) =>
        rightSidebarValue == false ? "100%" : "81%"}
	min-height: 100vh;
	
	// Tablets (iPad)
	@media (min-width: 768px) and (max-width: 1024px) {
		widht: ${({ rightSidebarValue }) =>
            rightSidebarValue == false ? "100%" : "65%"};
	}
	
	// Tablets pequeñas
	@media (min-width: 600px) and (max-width: 767px) {
		widht: ${({ rightSidebarValue }) =>
            rightSidebarValue == false ? "100%" : "60%"};
	}
`;

const HeaderRightIcons = Styled("div", props)`
	display: flex;
	justify-content: flex-end;
	align-items: center;
`;

const MainHeader = Styled("div", props)`
	.ant-layout-header, .ant-menu-horizontal{
		background: #2e3f50 !important;
	}
`;

const MainContentArea = Styled("div", props)`

	.breadcrumb-header {
		.page-content-sub-header {
			padding-top: 10px !important;
			padding-bottom: 10px !important;

			${({ cssSettings }) =>
        cssSettings && cssSettings.headerMenuMode == "horizontal"
            ? `border-bottom: 0px`
            : ""};
		}

		.breadcrumb-left-border {
			border-left: none !important;
		}

		.ant-card-body {
			padding: ${({ cssSettings }) =>
        cssSettings && cssSettings.headerMenuMode == "horizontal"
            ? `0px ${cssSettings.leftRightPadding}`
            : "0px"};
			margin: ${({ cssSettings }) =>
        cssSettings && cssSettings.headerMenuMode == "horizontal"
            ? "0px"
            : "0px 16px 0"};
		}
	}

	.dashboard-page-content-container {
		padding: ${({ cssSettings }) =>
        cssSettings && cssSettings.headerMenuMode == "horizontal"
            ? `0px ${cssSettings.leftRightPadding}`
            : "0px"};
		margin: ${({ cssSettings }) =>
        cssSettings && cssSettings.headerMenuMode == "horizontal"
            ? "0px"
            : "0px 16px 0"};
		${({ cssSettings }) =>
        cssSettings && cssSettings.headerMenuMode == "horizontal"
            ? `border-top: 1px solid #cbd6e2`
            : ""};
	}

	.page-content-container {
		min-height: calc(100vh - 165px);
	}

	.email-page-content-container {
		min-height: calc(100vh - 202px);
	}

	.page-content-container, .email-page-content-container {
		${({ cssSettings }) =>
        cssSettings && cssSettings.headerMenuMode == "horizontal"
            ? `border-top: 1px solid #cbd6e2`
            : ""};

		.ant-card-body {
			padding: ${({ cssSettings }) =>
        cssSettings && cssSettings.headerMenuMode == "horizontal"
            ? `0px ${cssSettings.leftRightPadding}`
            : "0px"};
			margin: ${({ cssSettings }) =>
        cssSettings && cssSettings.headerMenuMode == "horizontal"
            ? "0px"
            : "0px 16px 0"};
			padding-top: 30px;
		}
	}

	.setting-sidebar {
		margin-left: ${({ cssSettings }) =>
        cssSettings && cssSettings.headerMenuMode == "horizontal"
            ? `${cssSettings.leftRightPadding}`
            : "0px"};
	}

	`;
//for right side bar//
const RightSidebar = Styled("div", props)`
	width: ${({ rightSidebarValue }) => (rightSidebarValue == false ? "0%" : "25%")};
	
	// Tablets (iPad)
	@media (min-width: 768px) and (max-width: 1024px) {
		width: ${({ rightSidebarValue }) => (rightSidebarValue == false ? "0%" : "35%")};
	}
	
	// Tablets pequeñas
	@media (min-width: 600px) and (max-width: 767px) {
		width: ${({ rightSidebarValue }) => (rightSidebarValue == false ? "0%" : "45%")};
	}

    // Celulares
    @media (max-width: 599px) {
		width: ${({ rightSidebarValue }) => (rightSidebarValue == false ? "0%" : "0%")};
    }
`;
export {
    Div,
    MainArea,
    HeaderRightIcons,
    MainContentArea,
    MainHeader,
    RightSidebar,
};
