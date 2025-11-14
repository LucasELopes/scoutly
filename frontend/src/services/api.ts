import axios from "axios";

const baseApi = axios.create({
  baseURL: "http://localhost:8000/", // sua API
  withCredentials: true
});

export default baseApi;
