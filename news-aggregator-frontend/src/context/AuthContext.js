import { createContext, useEffect, useState } from "react";

export const AuthContext = createContext();

const updateLocalStorage = (data) => {
  if (data) {
    localStorage.setItem("access_token", JSON.stringify(data));
  } else {
    localStorage.removeItem("access_token");
  }
};

export const AuthContextProvider = ({ children }) => {
  const [currentUser, setCurrentUser] = useState(
    JSON.parse(localStorage.getItem("access_token")) || null
  );

  const updateUser = (data) => {
    setCurrentUser(data);
    updateLocalStorage(data);
  };

  useEffect(() => {
    updateLocalStorage(currentUser);
  }, [currentUser]);

  return (
    <AuthContext.Provider value={{ currentUser, updateUser }}>
      {children}
    </AuthContext.Provider>
  );
};
