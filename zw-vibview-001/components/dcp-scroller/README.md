### 下拉刷新组件

+ 极度顺滑，低耦合
+ 相对定位，无需考虑层级问题



示例
1. 下载插件解压放在通用组件文件夹components内，无需手动引入

2. 使用
```html
<template>
  <dcp-scroller :isLoading="isLoading" @refresh="refresh">
  	<!-- 内容 -->
    <!-- 建议此处盒子设置一个最小高度，保证无内容时此区域都能触摸下拉 -->
  </dcp-scroller>
</template>

<script>
export default {
  data() {
    return {
      isLoading: false
    };
  },
  methods: {
    refresh() {
      this.isLoading = true;
      setTimeout(() => {
        this.isLoading = false;
      }, 1000);
    }
  }
};
</script>

```

