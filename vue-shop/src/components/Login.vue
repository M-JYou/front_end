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
		</div>
	</div>
</template>
<script>
	export default {};
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
				width: 120px;
				height: 120px;
				position: absolute;
				top: -60px;
				right: 140px;
				background-color: #fff;
				border-radius: 100px;
				text-align: center;
				line-height: 150px;
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
		}
	}
</style>
