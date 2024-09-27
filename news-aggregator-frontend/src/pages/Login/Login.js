import { useEffect, useState } from "react";
import { Link, useNavigate } from "react-router-dom";
import apiRequest from "../../lib/api.js";
import "./Login.scss";
import { checkUserPreferences } from "../../utils/preferenceUtils.js";
import { validateEmail } from "../../utils/helperFunctions.js";
import { message } from "antd";

const Login = () => {
  const [isLoading, setIsLoading] = useState(false);
  const navigate = useNavigate();

  useEffect(()=>{
    const accessToken = JSON.parse(localStorage.getItem("access_token"));
    if (accessToken) {
      checkUserPreferences(accessToken, navigate);
    }
  },[])

  const storeToken = (token) => {
    localStorage.setItem("access_token", JSON.stringify(token));
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setIsLoading(true);
    const formData = new FormData(e.target);

    const email = formData.get("email");
    validateEmail(email);
    const password = formData.get("password");

    try {
      const res = await apiRequest.post("/login", {
        email: email,
        password: password,
      });

      if (res.status === 200) {
        const { access_token } = res.data;
        storeToken(access_token);
        message.success("Login successful")
        checkUserPreferences(access_token, navigate, "/");
      }

    } catch (err) {
      message.error(err.response?.data?.message)
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <div className="login">
      <div className="formContainer">
        <form onSubmit={handleSubmit}>
          <h1>Welcome back</h1>
          <input
            name="email"
            required
            minLength={3}
            maxLength={150}
            type="text"
            placeholder="Email"
          />
          <input
            name="password"
            type="password"
            required
            placeholder="Password"
          />
          <button disabled={isLoading}>Login</button>
          <Link to="/register">{"Don't"} you have an account?</Link>
        </form>
      </div>
    </div>
  );
}

export default Login;
