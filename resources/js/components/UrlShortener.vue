<template>
    <div class="container">
      <h1>URL Shortener</h1>
      <form @submit.prevent="shortenUrl" class="form">
        <input v-model="url" type="text" placeholder="Enter URL" required />
        <button type="submit">Shorten URL</button>
      </form>
      <div v-if="shortUrl" class="short-url">
        <h3>Short URL: <a :href="shortUrl" target="_blank">{{ shortUrl }}</a></h3>
      </div>
      <div v-if="error" class="error">
        <h3>{{ error }}</h3>
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref } from 'vue';
  import axios from 'axios';
  
  const url = ref('');
  const shortUrl = ref('');
  const error = ref('');
  
  const shortenUrl = async () => {
    try {
      error.value = '';
      const response = await axios.post('http://127.0.0.1:8000/shorten', { url: url.value });
      shortUrl.value = response.data.short_url;
    } catch (err) {
      if (err.response && err.response.data.error) {
        error.value = err.response.data.error;
      } else {
        error.value = 'An error occurred.';
      }
    }
  };
  </script>
  
  <style scoped>
.container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 40px;
}

h1 {
  margin-bottom: 20px;
  font-size: 2rem;
}

.form {
  display: flex;
  align-items: center;
  justify-content: center;
}

.form input {
  padding: 10px;
  font-size: 1rem;
  border: 1px solid #ccc;
  border-radius: 4px 0 0 4px;
  width: 500px;
}

.form button {
  padding: 10px 20px;
  font-size: 1rem;
  border: 1px solid #ccc;
  border-left: none;
  border-radius: 0 4px 4px 0;
  background-color: #007bff;
  color: white;
  cursor: pointer;
}

.form button:hover {
  background-color: #0056b3;
}

.short-url, .error {
  margin-top: 20px;
  font-size: 1rem;
  text-align: center;
}

.error {
  color: red;
}
</style>
  