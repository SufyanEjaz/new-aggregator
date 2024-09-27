import React, { useEffect, useState } from 'react';
import './navbar.scss';
import { Link, useLocation, useNavigate } from 'react-router-dom';
import apiRequest from '../../lib/api';
import useNavbarScrollEffect from "../../hooks/useNavbarScrollEffect.js";

const Navbar = () => {
  const [checkUser, setUser] = useState(JSON.parse(localStorage.getItem("access_token")));
  const navigateToLogout = useNavigate();
  const location = useLocation();

  useNavbarScrollEffect();

  const checkUserLoggedIn = () => {
    const token = JSON.parse(localStorage.getItem("access_token"));
    setUser(token);
  };

  useEffect(() => {
    const handleStorageChange = () => {
      checkUserLoggedIn();
    };

    window.addEventListener('storage', handleStorageChange);

    return () => {
      window.removeEventListener('storage', handleStorageChange);
    };
  }, []);


  const handleLogOut = async () => {
    try {
      await apiRequest.post('/logout', {}, {
        headers: {
          Authorization: `Bearer ${checkUser}`
        },
      });
      setUser(null);
      localStorage.removeItem('access_token');
      navigateToLogout("/login")
    } catch (error) {
      console.log("err", error)
      console.error('Logout error:', error.response ? error.response.data : error.message);
    }
  };

  return (
    <>
      <nav className="navbar">
        <div className="navbar-container container">
          <div className="navbox">
            <input type="checkbox" name="" id="" />
            <div className="hamburger-lines">
              <span className="line line1"></span>
              <span className="line line2"></span>
              <span className="line line3"></span>
            </div>
            <ul className="menu-items">
            {location.pathname !== "/preferences" && (
              <li><Link to="/preferences"><img src='/settings.svg' className='setting'/></Link></li>
            )}
              {checkUser === null ? (
                <>
                  <li><Link to="/login">Sign in</Link></li>
                  <li><Link to="/register">Register</Link></li>
                </>
              ) : (
                <>
                  <li style={{ cursor: 'pointer' }} onClick={handleLogOut}>Logout</li>
                </>
              )}
            </ul>
            <h1 className="logo">
              <Link to="/">
                <img src="/logo.png" height={100} width={50} alt="logo" className='logo'/>
              </Link>
            </h1>
          </div>
        </div>
      </nav>
    </>
  );
};

export default Navbar;
