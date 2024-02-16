
import axios from 'axios';
const BASE_URL = import.meta.env.VITE_BASE_URL_API

export async function getUserRole() {
  try {
    const response = await axios.get(BASE_URL + '/user/detail', {
      headers: {
        Authorization: 'Bearer ' + localStorage.getItem('access_token')
      }
    });
    console.log(response.data.role)
    return response.data.role;
  } catch (error) {
    console.error('Error fetching user role:', error);
    return null; 
  }
}
