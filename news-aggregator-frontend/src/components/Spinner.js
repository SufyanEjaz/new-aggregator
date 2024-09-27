import LoadingOutlined from "@ant-design/icons/LoadingOutlined";
import {Spin} from "antd";

const Spinner = ({ fontSize, tip, color }) => {
    let size = fontSize < 10 ? 24 : fontSize;
    let tipText = tip !== undefined ? tip : '';
    let fontColor = color !== undefined ? 'white' : ''
    const antIcon = (
        <LoadingOutlined style={{ fontSize: size, color: fontColor }} spin />
    );

    return (
        <Spin tip={tipText} indicator={antIcon} />
    )
};

export default Spinner;