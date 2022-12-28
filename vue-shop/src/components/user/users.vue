
<template>
	<div>
		<el-breadcrumb separator-class="el-icon-arrow-right">
			<el-breadcrumb-item :to="{ path: '/home' }">首页</el-breadcrumb-item>
			<el-breadcrumb-item>用户管理</el-breadcrumb-item>
			<el-breadcrumb-item>用户列表</el-breadcrumb-item>
		</el-breadcrumb>
		<el-card>
			<el-row :gutter="20">
				<el-col :span="8">
					<!-- 搜索输入框 -->
					<el-input
						placeholder="请输入姓名"
						class="input-with-select"
						v-model="queryInfo.query"
						clearable
						@clear="getUserList()"
						@change="getUserList()"
					>
						<el-button slot="append" icon="el-icon-search" @click="getUserList()"></el-button>
					</el-input>
				</el-col>
				<el-col :span="4">
					<!-- 添加用户按钮 -->
					<el-button type="primary" @click="addDialogVisible = true">添加用户</el-button>
				</el-col>
			</el-row>
			<!-- 添加用户对话框 -->
			<el-dialog
				title="添加用户"
				:visible.sync="addDialogVisible"
				width="50%"
				@close="addDialogClosed()"
			>
				<el-form
					:model="addRuleForm"
					:rules="addRules"
					ref="ruleForm"
					label-width="70px"
					class="demo-ruleForm"
				>
					<el-form-item label="用户名" prop="username">
						<el-input v-model="addRuleForm.username"></el-input>
					</el-form-item>
					<el-form-item label="密码" prop="password">
						<el-input v-model="addRuleForm.password" show-password></el-input>
					</el-form-item>
					<el-form-item label="邮箱" prop="email">
						<el-input v-model="addRuleForm.email"></el-input>
					</el-form-item>
					<el-form-item label="手机" prop="mobile">
						<el-input v-model="addRuleForm.mobile"></el-input>
					</el-form-item>
				</el-form>
				<span slot="footer" class="dialog-footer">
					<el-button @click="addDialogVisible = false">取 消</el-button>
					<el-button type="primary" @click="addUsers">确 定</el-button>
				</span>
			</el-dialog>
			<!-- 用户列表表格 -->
			<el-table
				:data="userList"
				border
				stripe
				:header-cell-style="{ textAlign: 'center' }"
				:cell-style="{ textAlign: 'center' }"
			>
				<el-table-column label="序号" type="index"> </el-table-column>
				<el-table-column label="姓名" prop="username"> </el-table-column>
				<el-table-column label="邮箱" prop="email"> </el-table-column>
				<el-table-column label="电话" prop="mobile"> </el-table-column>
				<el-table-column label="角色" prop="role_name"> </el-table-column>
				<el-table-column label="状态">
					<!-- 自定义状态列模板 -->
					<template slot-scope="scope">
						<el-switch v-model="scope.row.mg_state" @change="userStateChange(scope.row)">
						</el-switch>
					</template>
				</el-table-column>
				<el-table-column label="操作" width="175px">
					<template>
						<el-button type="primary" size="mini" icon="el-icon-edit"></el-button>
						<el-button type="danger" size="mini" icon="el-icon-delete"></el-button>
						<el-tooltip effect="dark" content="分配角色" placement="top" :enterable="false">
							<el-button type="warning" size="mini" icon="el-icon-setting"></el-button>
						</el-tooltip>
					</template>
				</el-table-column>
			</el-table>
			<el-pagination
				@size-change="handleSizeChange"
				@current-change="handleCurrentChange"
				:current-page="queryInfo.pagenum"
				:page-sizes="[1, 2, 5, 10]"
				:page-size="queryInfo.pagesize"
				layout="total, sizes, prev, pager, next, jumper"
				:total="total"
			>
			</el-pagination>
		</el-card>
	</div>
</template>
<script>
	export default {
		data() {
			return {
				userList: [], //用户列表数据
				queryInfo: {
					query: '', //查询参数
					pagenum: 1, //当前页码
					pagesize: 2, //每页显示条数
				},
				// 用户列表总条目数量
				total: 0,
				// 是否显示对话框
				addDialogVisible: false,
				// 添加用户表单对象
				addRuleForm: {
					username: '',
					password: '',
					email: '',
					mobile: '',
				},
				// 添加用户对象表单验证
				addRules: {
					username: [
						{ required: true, message: '请输入用户名', trigger: 'blur' },
						{ min: 3, max: 10, message: '长度在 3 到 10 个字符', trigger: 'blur' },
					],
					password: [
						{ required: true, message: '请输入密码', trigger: 'blur' },
						{ min: 6, max: 15, message: '长度在 3 到 15个字符', trigger: 'blur' },
					],
					email: [
						{ required: true, message: '请输入邮箱', trigger: 'blur' },
						{
							pattern: /\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/,
							message: '邮箱格式不正确',
							trigger: 'blur',
						},
					],
					mobile: [
						{ required: true, message: '请输入用户名', trigger: 'blur' },
						{
							pattern: /^(0|86|17951)?(13[0-9]|15[012356789]|166|17[3678]|18[0-9]|14[57])[0-9]{8}$/,
							message: '手机号码格式不正确',
							trigger: 'blur',
						},
					],
				},
			};
		},
		created() {
			this.getUserList();
		},
		computed: {},
		methods: {
			async getUserList() {
				const { data: res } = await this.$http.get('/users', {
					params: this.queryInfo,
				});
				if (res.meta.status !== 200) {
					return this.$message.error('获取用户列表数据失败');
				}
				this.userList = res.data.users;
				this.total = res.data.total;
			},
			// 每页显示条数事件
			handleSizeChange(pagesize) {
				this.queryInfo.pagesize = pagesize;
				this.getUserList();
			},
			// 改变当前页事件
			handleCurrentChange(pagenum) {
				this.queryInfo.pagenum = pagenum;
				this.getUserList();
			},
			// 用户状态改变事件
			async userStateChange(userInfo) {
				const { data: res } = await this.$http.put(
					`users/${userInfo.id}/state/${userInfo.mg_state}`
				);
				if (res.meta.status !== 200) {
					userInfo.mg_state = !userInfo.mg_state;
					return this.$message.error('状态更新失败');
				}
				this.$message.success('状态更新成功');
			},
			// 重置添加用户对话框
			addDialogClosed() {
				this.$refs.ruleForm.resetFields();
			},
			// 添加用户事件
			addUsers () {
				// 数据校验失败时，不发送请求
				this.$refs.ruleForm.validate(async(validate) => {
					if (!validate) {
						return
					}
					const { data: res } = await this.$http.post('users', this.addRuleForm)
					if (res.meta.status !== 201) { 
					// 添加失败消息提示框
						return this.$message.error("用户添加失败")
					}
					// 添加成功消息提示框
					this.$message.success("用户添加成功")
					// 刷新用户数据列表
					this.getUserList()
					// 关闭对话框
					this.addDialogVisible = false
				})
			}
		},
	};
</script>
<style lang="scss" scoped></style>
