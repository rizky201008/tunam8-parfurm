<template>
    <main>
        <Navbar />
        <section class="login-section">
            <div class="container">
                <div class="form-box shadow" :class="{ 'register-form': isRegister }">
                    <div class="padding-container">
                        <div class="form-value">
                            <transition name="fade" mode="out-in">
                                <form v-if="!isRegister && !isEmailForgotPassword && !isForgotPassword" key="login-form"
                                    @submit.prevent="onSubmit">
                                    <h2>Login</h2>
                                    <div class="inputbox">
                                        <ion-icon name="mail-outline"></ion-icon>
                                        <input class="input-type" type="email" v-model="loginEmail" required
                                            @focus="enableEmailAutocomplete" autocomplete="off">
                                        <label for="">Email</label>
                                    </div>
                                    <div class="inputbox">
                                        <ion-icon name="lock-closed-outline"></ion-icon>
                                        <input class="input-type" type="password" v-model="loginPassword" required
                                            @focus="enablePasswordAutocomplete" autocomplete="off">
                                        <label for="">Password</label>
                                    </div>
                                    <div class="forget">
                                        <label for=""><input type="checkbox" v-model="rememberMe">Remember Me
                                            <br><a class="text-right" @click="toggleEmailForgotPassword"
                                                style="cursor: pointer">Forget Password</a></label>
                                    </div>
                                    <button class="login-button" type="submit" style="color:white">Log in</button>
                                    <div class="register">
                                        <p>Don't have an account <a @click="toggleForm" style="cursor: pointer">Register</a>
                                        </p>
                                    </div>
                                </form>
                                <form v-else-if="isEmailForgotPassword" key="email-forgot-password-form">
                                    <h2>Forgot
                                        Password?</h2>
                                    <div class="inputbox">
                                        <ion-icon name="mail-outline"></ion-icon>
                                        <input class="input-type" type="email" v-model="enterEmail" required>
                                        <label for="">Enter Your Email</label>
                                    </div>

                                    <!-- <button class="login-button" type="submit" style="color:white" @click="toggleForgotPassword">Send Email</button> -->
                                    <button class="login-button" style="color:white" @click="toggleForgotPassword">Send
                                        Email</button>
                                    <div class="register">
                                        <p>Already have an account <a @click="toggleEmailForgotPassword"
                                                style="cursor: pointer">Log in</a>
                                        </p>
                                    </div>
                                </form>
                                <form v-else-if="isForgotPassword">
                                    <h2>Forgot Password?</h2>
                                    <span class="d-flex justify-content-center">Reset Your Password</span>
                                    <div class="inputbox">
                                        <ion-icon name="lock-closed-outline"></ion-icon>
                                        <input class="input-type" type="password" v-model="forgetPassword" required>
                                        <label for="">Re-Type Old Password</label>
                                    </div>
                                    <div class="inputbox">
                                        <ion-icon name="lock-closed-outline"></ion-icon>
                                        <input class="input-type" type="password" v-model="createForgetPassword" required>
                                        <label for="">Create New Password</label>
                                    </div>
                                    <button class="login-button" type="submit" style="color:white">Register</button>
                                    <div class="register">
                                        <p>Already have an account <a @click="toggleForgotPassword"
                                                style="cursor: pointer">Log in</a>
                                        </p>
                                    </div>
                                </form>
                                <form v-else key="register-form" @submit.prevent="onRegist">
                                    <h2>Register</h2>
                                    <div class="inputbox">
                                        <ion-icon name="person-outline"></ion-icon>
                                        <input class="input-type" type="input" v-model="registerName" required>
                                        <label for="">Name</label>
                                    </div>
                                    <div class="inputbox">
                                        <ion-icon name="mail-outline"></ion-icon>
                                        <input class="input-type" type="email" v-model="registerEmail" required>
                                        <label for="">Email</label>
                                    </div>
                                    <div class="inputbox">
                                        <ion-icon name="lock-closed-outline"></ion-icon>
                                        <input class="input-type" type="password" v-model="registerPassword" required>
                                        <label for="">Create Password</label>
                                    </div>
                                    <button class="login-button" type="submit" style="color:white">Register</button>
                                    <div class="register">
                                        <p>Already have an account <a @click="toggleForm" style="cursor: pointer">Log in</a>
                                        </p>
                                    </div>
                                </form>
                            </transition>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
</template>
<script>
import Navbar from '@/components/LoginNavbar.vue';
import axios from "axios";
const BASE_URL = import.meta.env.VITE_BASE_URL_API
export default {
    name: 'LoginPage',
    components: {
        Navbar,
    },
    data() {
        return {
            isRegister: false,
            isForgotPassword: false,
            isEmailForgotPassword: false,
            // Login Inpput
            loginEmail: "",
            loginPassword: "",
            // Register Input
            registerName: "",
            registerEmail: "",
            registerPassword: "",
            // Forgot Password Input
            forgotPassword: "",
            createForgotPassword: "",

            rememberMe: false,

            emailAutocomplete: 'off',
            passwordAutocomplete: 'off'
        };
    },
    mounted() {
        const inputElements = this.$el.querySelectorAll('input');
        inputElements.forEach((input) => {

            if (input.value) {
                this.moveLabelUp(input);
            }
            input.addEventListener('input', () => {
                if (input.value) {
                    this.moveLabelUp(input);
                } else {
                    this.moveLabelDown(input);
                }
            });
        });
        const accessToken = localStorage.getItem('access_token');
        if (accessToken) {
            axios.get(BASE_URL + '/user/detail', {
                headers: {
                    Authorization: "Bearer " + localStorage.getItem('access_token')
                }
            })
                .then(response => {
                    const { role } = response.data;
                    if (role === 'admin') {
                        this.$router.push('/admin/dashboard');
                    } else if (role === 'user') {
                        this.$router.push('/dashboard');
                    }
                })
                .catch(error => {
                    console.error(error);
                    // Handle error if necessary
                });
        }
    },

    methods: {
        enableEmailAutocomplete() {
            this.emailAutocomplete = 'email';
        },
        enablePasswordAutocomplete() {
            this.passwordAutocomplete = 'current-password';
        },
        toggleForm() {
            this.isRegister = !this.isRegister;
        },
        toggleEmailForgotPassword() {
            this.isEmailForgotPassword = !this.isEmailForgotPassword;
        },
        toggleForgotPassword() {
            this.isEmailForgotPassword = false;
            this.isRegister = false;
            this.isForgotPassword = !this.isForgotPassword;
        },
        moveLabelUp(input) {
            const label = input.nextElementSibling;
            label.style.top = '-5px';
        },

        moveLabelDown(input) {
            const label = input.nextElementSibling;
            if (!input.value) {
                label.style.top = '50%';
            }
        },


        async onSubmit() {
            try {
                const response = await axios.post(BASE_URL + '/account/login', {
                    email: this.loginEmail,
                    password: this.loginPassword
                });

                this.$notify({
                    type: 'success',
                    title: 'Success',
                    text: response.data.message,
                    color: 'green'
                });
                console.log(response.data);

                const { role } = response.data.user;

                if (role === 'admin') {
                    localStorage.setItem('access_token', response.data.token);
                    this.$router.push('/admin/dashboard');
                } else if (role === 'user') {
                    localStorage.setItem('access_token', response.data.token);
                    this.$router.push('/dashboard');
                }


            } catch (error) {
                console.error(error);

                if (error.response && error.response.data.message) {
                    const errorMessage = error.response.data.message;
                    // Display notification with red color
                    this.$notify({
                        type: 'error',
                        title: 'Error',
                        text: errorMessage,
                        color: 'red'
                    });
                }
            }
        },
        async onRegist() {
            try {
                const response = await axios.post(BASE_URL + '/account/register', {
                    name: this.registerName,
                    email: this.registerEmail,
                    password: this.registerPassword
                });

                console.log(response.data);
                this.$router.push('/login');
                // Reload the page after redirection
                window.location.reload();
            } catch (error) {
                console.error(error);

                if (error.response && error.response.data.message) {
                    const errorMessage = error.response.data.message;
                    // Display notification with red color
                    this.$notify({
                        type: 'error',
                        title: 'Error',
                        text: errorMessage,
                        color: 'red'
                    });
                }
            }
        }

    }
}
</script>
<style scoped>
.login-section {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    width: 100%;
    /* background-image: url('../assets/LandingPage/Background.png'); */
    background-color: white;
    background-position: center;
    background-size: cover;
}

.form-box {
    position: relative;
    width: 400px;
    height: 450px;
    background: transparent;
    border: 1px solid red;
    border-radius: 20px;
    backdrop-filter: blur(15px);
    display: flex;
    justify-content: center;
    align-items: center;

}

h2 {
    font-size: 2em;
    color: black;
    text-align: center;
}

.inputbox {
    position: relative;
    margin: 30px 0;
    width: 310px;
    border-bottom: 1px solid black;
}

.inputbox label {
    position: absolute;
    top: 50%;
    left: 5px;
    transform: translateY(-50%);
    color: black;
    font-size: 1em;
    pointer-events: none;
    transition: .5s;
}

.input-type:focus~label,
.input-type:valid~label {
    top: -5px;
}

.inputbox input {
    width: 100%;
    height: 50px;
    background: transparent;
    border: none;
    outline: none;
    font-size: 1em;
    padding: 0 35px 0 5px;
    color: black;
}

.inputbox ion-icon {
    position: absolute;
    right: 8px;
    color: black;
    font-size: 1.2em;
    top: 20px;
}

.forget {
    margin: -15px 0 15px;
    font-size: .9em;
    color: black;
    display: flex;
    justify-content: space-between;
}

.forget label input {
    margin-right: 3px;

}

.forget label a {
    color: black;
    text-decoration: none;
}

.forget label a:hover {
    text-decoration: underline;
}

.login-button {
    width: 100%;
    height: 40px;
    border-radius: 40px;
    background: black;
    border: none;
    outline: none;
    cursor: pointer;
    font-size: 1em;
    font-weight: 600;
}

.register {
    font-size: .9em;
    color: black;
    text-align: center;
    margin: 25px 0 10px;
}

.register p a {
    text-decoration: none;
    color: black;
    font-weight: 600;
}

.register p a:hover {
    text-decoration: underline;
}

.container {
    padding: 20px;
    display: flex;
    justify-content: center;
    align-items: center;
}

.padding-container {
    padding: 20px;
    display: flex;
    justify-content: center;
    align-items: center;
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.5s;
}

.fade-enter,
.fade-leave-to {
    opacity: 0;
}
</style>