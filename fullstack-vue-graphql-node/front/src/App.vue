<template>
  <div>
    <div id="slogan" class="text-center">
      <h1>Namegator</h1>
      <br />
      <h6 class="text-secondary">
        Gerador de nome utilizando Vue.js, GraphQL e Node
      </h6>
    </div>
    <div id="main">
      <DomainList></DomainList>
    </div>
  </div>
</template>

<script>
import 'bootstrap/dist/css/bootstrap.css';
import 'font-awesome/css/font-awesome.css';
import DomainList from './components/DomainList.vue';

export default {
  name: 'App',
  components: {
    DomainList,
  },
};
</script>

<style>
#slogan {
  margin-top: 30px;
  margin-bottom: 30px;
}

#main {
  background-color: #f1f1f1;
  padding-top: 30px;
  padding-bottom: 30px;
}

.badge-info {
  color: #fff;
  background-color: #17a2b8;
}

.text-right {
  text-align: right;
}

.row {
  align-items: center;
}
</style>
