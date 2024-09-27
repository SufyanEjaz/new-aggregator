import React from 'react';
import './Loader.scss';
import Spinner from '../Spinner';


const Loader = ({tip}) => {
    return (
        <div id="preloader">
        <div id="loader">
            <Spinner fontSize={50} tip={tip ?? ('loading')} />
        </div>
    </div>
    );
};

export default Loader;
