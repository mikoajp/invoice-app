<template>
    <div class="not-found-container">
        <h1 class="not-found-title">Page not found</h1>
        <router-link to="/" class="home-link">Back to home</router-link>
    </div>
</template>

<style scoped>
.not-found-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100vh;
    text-align: center;
    background-color: #f0f0f0;
    font-family: 'Arial', sans-serif;
}

.not-found-title {
    font-size: 3em;
    color: #ff6b6b;
    margin-bottom: 20px;
    animation: fadeIn 2s ease-in-out;
}

.home-link {
    display: inline-block;
    padding: 10px 20px;
    font-size: 1.2em;
    color: #ffffff;
    background-color: #4a90e2;
    text-decoration: none;
    border-radius: 5px;
    transition: background-color 0.3s ease;
}

.home-link:hover {
    background-color: #357ABD;
}

@keyframes fadeIn {
    0% {
        opacity: 0;
        transform: translateY(-20px);
    }
    100% {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
