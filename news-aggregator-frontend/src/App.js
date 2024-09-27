import { createBrowserRouter, RouterProvider } from "react-router-dom";
import { AuthContextProvider } from './context/AuthContext';
import HomePage from './pages/HomePage/HomePage';
import { RequireAuth } from './pages/layout/Layout';
import Login from './pages/Login/Login';
import Register from './pages/register/Register';
import ArticleDetail from './pages/ArticleDetail/ArticleDetail';
import Preferences from "./pages/Preferences/Preferences";
import ErrorPage from "./pages/ErrorPage/ErrorPage";
import NotFound from "./pages/NotFound/NotFound";

function App() {

  const privateRoutes = [
    { path: "/", element: <HomePage /> },
    { path: "article/:id", element: <ArticleDetail /> },
    { path: "/preferences", element: <Preferences /> },
  ];

  const router = createBrowserRouter([
    {
      path: "/",
      element: <RequireAuth />,
      children: privateRoutes,
    },
    {
      path: "/login",
      element: <Login />,
    },
    {
      path: "/register",
      element: <Register />,
    },
    {
      path: "/error",
      element: <ErrorPage />,
    },
    {
      path: "*",  
      element: <NotFound />,
    },
  ]);

  return (
    <AuthContextProvider>
        <RouterProvider router={router} />
    </AuthContextProvider>
  );
}

export default App;
