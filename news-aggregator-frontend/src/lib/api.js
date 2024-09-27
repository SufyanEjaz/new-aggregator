import axios from "axios"


const apiRequest = axios.create({
    baseURL: "http://localhost:8000/api",
    headers: {
      "Content-Type": "application/json"
    }
  });


// Interceptor to handle errors globally
apiRequest.interceptors.response.use(
  (response) => response,
  (error) => {
    console.error("API Request Error:", error);
    return Promise.reject(error);
  }
);

export default apiRequest;