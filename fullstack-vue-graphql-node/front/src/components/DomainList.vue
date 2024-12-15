<template>
  <div>
    <div id="main">
      <div class="container">
        <div class="row">
          <div class="col-md">
            <AppItemList
              title="Prefixos"
              type="prefix"
              v-bind:items="items.prefix"
              v-on:addItem="addItem"
              v-on:deleteItem="deleteItem"
            ></AppItemList>
          </div>
          <div class="col-md">
            <AppItemList
              title="Sufixos"
              type="suffix"
              v-bind:items="items.suffix"
              v-on:addItem="addItem"
              v-on:deleteItem="deleteItem"
            ></AppItemList>
          </div>
        </div>
        <br />
        <h5>
          Domínios <span class="badge badge-info">{{ domains.length }}</span>
        </h5>
        <div class="card">
          <div class="card-body">
            <ul class="list-group">
              <li
                class="list-group-item"
                v-for="domain in domains"
                v-bind:key="domain.name"
              >
                <div class="row">
                  <div class="col-md">
                    {{ domain.name }}
                  </div>
                  <div class="col-md text-right">
                    <a
                      v-bind:href="domain.checkout"
                      target="_blank"
                      class="btn btn-info"
                    >
                      <span class="fa fa-shopping-cart"></span>
                    </a>
                  </div>
                </div>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import 'bootstrap/dist/css/bootstrap.css';
import 'font-awesome/css/font-awesome.css';
import AppItemList from './AppItemList.vue';
import axios from 'axios';

export default {
  name: 'DomainList',
  components: {
    AppItemList,
  },
  data: function () {
    return {
      items: {
        prefix: [],
        suffix: [],
      },
    };
  },
  methods: {
    addItem(item) {
      axios({
        url: 'http://localhost:4000',
        method: 'POST',
        data: {
          query: `
            mutation ($item: ItemInput) {
              newItem: saveItem(item: $item) {
                id
                type
                description
              }
            }
          `,
          variables: {
            item,
          },
        },
      }).then((response) => {
        const query = response.data;
        const newItem = query.data.newItem;

        this.items[item.type].push(newItem);
      });
    },
    deleteItem(item) {
      axios({
        url: 'http://localhost:4000',
        method: 'POST',
        data: {
          query: `
            mutation ($id: Int) {
              deleted: deleteItem(id: $id)
            }
          `,
          variables: {
            id: item.id,
          },
        },
      }).then(() => {
        this.getItems(item.type);
      });
    },
    getItems(type) {
      axios({
        url: 'http://localhost:4000',
        method: 'post',
        data: {
          query: `
            query ($type: String) {
              items: items(type: $type) {
                id
                type
                description
              }
          }
        `,
          variables: {
            type,
          },
        },
      }).then((res) => {
        const query = res.data;
        this.items[type] = query.data.items;
      });
    },
  },
  computed: {
    domains() {
      const domains = [];
      for (const prefix of this.items.prefix) {
        for (const suffix of this.items.suffix) {
          const name = prefix.description + suffix.description;
          const url = name.toLocaleLowerCase();

          const checkout = `https://cart.hostgator.com.br/?pid=d&sld=${url}&tld=.com.br&domainCycle=2`;
          domains.push({
            name,
            checkout,
          });
        }
      }

      return domains;
    },
  },
  created() {
    this.getItems('prefix');
    this.getItems('suffix');
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
