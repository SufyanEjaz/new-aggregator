import { useContext } from "react";
import { Navigate, Outlet } from "react-router-dom";
import Navbar from "../../components/Navbar/Navbar";
import { AuthContext } from "../../context/AuthContext";

function RequireAuth() {
  const { currentUser } = useContext(AuthContext);

  if (!currentUser && !localStorage.getItem("access_token")) {
    return <Navigate to="/login" />;
  } 
  
  return (
    <div className="layout">
      <div className="navbar">
        <Navbar />
      </div>
      <div className="content">
        <Outlet />
      </div>
    </div>
  );
}

export { RequireAuth };
