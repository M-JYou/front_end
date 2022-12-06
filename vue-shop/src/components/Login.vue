<template>
	<div class="login-containter">
		<div class="login-img">
			<img src="../assets/imgs/login_img.png" />
		</div>
		<div class="login-box">
			<!-- 头像 -->
			<div class="avatar-box">
				<img src="../assets/imgs/login_logo.png" />
			</div>
			<!-- 标题 -->
			<div class="title">电商后台管理系统</div>
			<!-- form表单 -->
			<el-form class="login-form" :model="loginForm" :rules="loginFormRules" ref="loginFormRef">
				<el-form-item prop="username">
					<el-input
						placeholder="请输入用户名"
						prefix-icon="iconfont icon-user"
						v-model="loginForm.username"
					></el-input>
				</el-form-item>
				<el-form-item prop="password">
					<el-input
						placeholder="请输入密码"
						prefix-icon="iconfont icon-lock_fill"
						v-model="loginForm.password"
						show-password
					></el-input>
				</el-form-item>
				<el-button type="primary" class="login-btn" @click="login">登录</el-button>
			</el-form>
		</div>
	</div>
</template>
<script>
	import axios from 'axios';
	export default {
		data() {
			return {
				loginForm: {
					username: 'admin',
					password: '123456',
				},
				loginFormRules: {
					username: [
						{ required: true, message: '请输入用户名', trigger: 'blur' },
						{ min: 3, max: 10, message: '长度在 3 到 10 个字符', trigger: 'blur' },
					],
					password: [
						{ required: true, message: '请输入密码', trigger: 'blur' },
						{ min: 3, max: 15, message: '长度在 3 到 15 个字符', trigger: 'blur' },
					],
				},
			};
		},
		methods: {
			login() {
				this.$refs.loginFormRef.validate(async (validate) => {
					const { data: res } = await this.$http.post('login', this.loginForm);
					// console.log(res);
					if (res.meta.status !== 200) {
						return this.$message.error('登录失败');
					}
					this.$message.success('登录成功');
					sessionStorage.setItem('userInfo', JSON.stringify(res.data));
					this.$router.push('/home');
				});
			},
		},
	};
</script>
<style lang="less" scoped>
	.login-containter {
		height: 100%;
		background-color: #93defe;
		display: flex;
		justify-content: center;
		align-items: center;

		.login-img {
			margin-right: 100px;
		}

		.login-box {
			width: 400px;
			height: 350px;
			background-color: #fff;
			padding: 50px;
			border-radius: 6px;
			box-sizing: border-box;
			position: relative;

			.avatar-box {
				position: absolute;
				width: 120px;
				height: 120px;
				top: -60px;
				right: 140px;
				background-color: #fff;
				border-radius: 100px;
				text-align: center;
				line-height: 110px;
				border: 5px solid #93defe;
			}

			.title {
				position: absolute;
				top: 100px;
				left: 50%;
				transform: translate(-50%);
				font-size: 18px;
				color: #444;
			}

			.login-form {
				position: absolute;
				bottom: 0;
				left: 50%;
				transform: translate(-50%);
				padding: 30px;
				width: 80%;
				box-sizing: border-box;
				.login-btn {
					width: 100%;
				}
			}
		}
	}
</style>
