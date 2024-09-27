import React from 'react';
import { Link } from 'react-router-dom';

function ErrorPage() {
    return (
        <div style={{ display: "flex", flexDirection: "column", justifyContent: "center", alignItems: "center" }}>
            <h1>Oops! Something went wrong.</h1>
            <p>We are sorry for the inconvenience. Please try again later.</p>
            <Link to="/">Go back to Home</Link>
        </div>
    );
}

export default ErrorPage;
