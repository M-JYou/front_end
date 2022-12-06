<template>
	<el-container>
		<!-- 头部 -->
		<el-header>
			<div class="left">
				<img src="../assets/imgs/shop.png" alt="" />
				<span>电商后台管理系统</span>
			</div>
			<div class="right">
				<span>用户名：{{ userInfo.username }}</span>
				<el-button type="danger" size="mini" @click="logout">退出</el-button>
			</div>
		</el-header>
		<el-container>
			<!-- 左侧栏 -->
			<el-aside :width="isCollapse ? '64px' : '200px'">
				<!-- 左侧菜单栏折叠功能 -->
				<div class="toggle-button" @click="isCollapse = !isCollapse">|||</div>
				<!-- 左侧菜单栏列表 -->
				<el-menu
					:default-active="$route.path"
					unique-opened
					router
					:collapse="isCollapse"
					:collapse-transition="false"
				>
					<el-submenu :index="item.id + ''" v-for="item in menuList" :key="item.id">
						<template slot="title">
							<i :class="iconObj[item.id]"></i>
							<span>{{ item.authName }}</span>
						</template>

						<el-menu-item
							:index="'/' + subItem.path"
							v-for="subItem in item.children"
							:key="subItem.id"
						>
							<i class="el-icon-menu"></i>
							{{ subItem.authName }}
						</el-menu-item>
					</el-submenu>
				</el-menu>
			</el-aside>
			<!-- 内容 -->
			<el-main>
				<router-view></router-view>
			</el-main>
		</el-container>
	</el-container>
</template>

<script>
	export default {
		data() {
			return {
				// 用户信息
				userInfo: null,
				// 菜单列表信息
				menuList: [],
				// 菜单列表图标对象
				iconObj: {
					201: 'iconfont icon-shouye',
					125: 'iconfont icon-users',
					103: 'iconfont icon-tijikongjian',
					101: 'iconfont icon-shangpin',
					102: 'iconfont icon-danju',
				},
				// 是否折叠菜单
				isCollapse: false,
			};
		},

		methods: {
			logout() {
				this.$confirm('确定退出登录吗?', '提示', {
					confirmButtonText: '确定',
					cancelButtonText: '取消',
					type: 'warning',
				})
					.then(() => {
						sessionStorage.clear();
						this.$router.push('/login');
					})
					.catch(() => {});
			},
			async getMenuList() {
				const { data: res } = await this.$http.get('/menus');
				if (res.meta.status !== 200) {
					return this.$message.error(res.meta.msg);
				}
				this.menuList = res.data;
				console.log(this.menuList);
			},
		},

		created() {
			this.userInfo = JSON.parse(sessionStorage.getItem('userInfo'));
			this.getMenuList();
		},
	};
</script>

<style lang="less" scoped>
	.el-container {
		height: 100%;
		.el-header {
			display: flex;
			background: url('../assets/imgs/header_bg.gif') repeat-x;
			height: 50px !important;
			justify-content: space-between;
			align-items: center;
			color: #fff;
			.left {
				display: flex;
				align-items: center;
				span {
					font-size: 20px;
				}
				img {
					width: 40px;
					margin-right: 20px;
				}
			}
			.right {
				span {
					margin-right: 10px;
				}
			}
		}
		.el-container {
			.el-aside {
				.toggle-button {
					background: #5e77a0;
					color: white;
					text-align: center;
					font-size: 10px;
					line-height: 24px;
					letter-spacing: 0.2rem;
					cursor: pointer;
				}
				.el-menu {
					border-right: 0;
					.iconfont {
						margin-right: 10px;
					}
				}
			}
			.el-main {
				background-color: #eaedf1;
			}
		}
	}
</style>
