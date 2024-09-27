import React, { useState } from 'react'
import "./register.scss";
import { Link, useNavigate } from "react-router-dom";
import apiRequest from '../../lib/api';
import { message } from "antd";

function Register() {
  const [isLoading, setIsLoading] = useState(false);
  const navigate = useNavigate();


  const handleSubmit = async (e) => {
    e.preventDefault();
    setIsLoading(true);
    const formData = new FormData(e.target);

    const username = formData.get("username");
    const email = formData.get("email");
    const password = formData.get("password");

    try {
      const res = await apiRequest.post("/register", {
        username,
        email,
        password,
      });

      if(res.status === 201){
        message.success(res.data.message)
        navigate("/login");
      }
    } catch (err) {
      message.error(err.response?.data?.message)
    } finally {
      setIsLoading(false);
    }
  };


  return (
    <>
      <div className="registerPage">
        <div className="formContainer">
          <form onSubmit={handleSubmit}>
            <h1>Create an Account</h1>
            <input name="username" type="text" placeholder="Username" required />
            <input name="email" type="text" placeholder="Email" required minLength={3} maxLength={150} />
            <input name="password" type="password" placeholder="Password" required />
            <button disabled={isLoading}>Register</button>
            <Link to="/login">Do you have an account?</Link>
          </form>
        </div>
      </div>
    </>
  )
}

export default Register
