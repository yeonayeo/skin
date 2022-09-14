<? include VIEWS_PATH.'/_include/head.php'; ?>

<div id="wrap">
  <? include VIEWS_PATH.'/_include/header.php'; ?>

  <main id="container" class="main_container" v-cloak>
    <div class="page_head">
      <h2 class="page_title">예약 관리</h2>
      <div class="input_search">
        <input type="search" placeholder="고객명을 검색하세요" style="width: 360px;" v-model="name" @keypress.enter="action_search">
        <button type="button" class="btn_input_search" @click="action_search"></button>
      </div>
    </div>
    <div class="schedule_search_wrap">
      <div class="result_txt">검색결과 {{ total_cnt }}건</div>
      <div class="table_list">
        <table>
          <colgroup>
            <col style="width: 200px;">
            <col style="width: 220px;">
            <col style="width: 220px;">
            <col style="width: 220px;">
            <col style="width: 220px;">
            <col style="width: auto;">
            <col style="width: 220px;">
          </colgroup>
          <thead>
            <tr>
              <th>구분</th>
              <th>고객명</th>
              <th>연락처</th>
              <th>관리일</th>
              <th>관리시간</th>
              <th>관리명</th>
              <th>관리실</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="client in list" style="cursor: pointer;" @click="popup_info(client.id, client.status_cd)">
              <td>{{ client.status }}</td>
              <td>{{ client.client_name }}</td>
              <td>{{ client.client_hp }}</td>
              <td>{{ client.booking_date }}</td>
              <td>{{ client.booking_time }}</td>
              <td>{{ client.manage_name }}</td>
              <td>{{ client.booking_room }}</td>
            </tr>
            <!-- 결과없음 -->
            <tr class="list_empty" v-if="total_cnt==0">
              <td colspan="7">검색 결과가 없습니다.</td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="pagination" v-if="pagination.page_range.length">
        <div class="navi">
          <button type="button" @click="pagination_page(pagination.prev_page)"><i class="material-icons">navigate_before</i></button>
        </div>
        <div class="pages">
          <button type="button" @click="pagination_page(page)" :class="{on: pagination.page == page}" v-for="page in pagination.page_range">{{ page }}</button>

        </div>
        <div class="navi">
          <button type="button" @click="pagination_page(pagination.next_page)"><i class="material-icons">navigate_next</i></button>
        </div>
      </div>
    </div>
  </main>

  <script>
    var FRONT = Vue.createApp({
      data() {
        return {
          res: RES,
          req: {},
          err: {},
          name: RES.name,
          list: RES.list,
          total_cnt: RES.total_cnt,
          pagination: RES.pagination
        }
      },
      mounted() {},
      methods: {
        action_search() {
          if(!this.name) return alert('고객명을 입력해주세요.');
          location.href = `/schedule/search?name=`+this.name;
        },
        popup_info(id, status_cd) {
          if(status_cd==1) {
            this.popup_book(id);
          } else if(status_cd==3) {
            this.popup_visit(id);
          }
        },
        popup_book(id) {
          popup_book = sunrise({
            data: {},
            target: '/schedule/popup_book?id='+id
          })
        },
        popup_visit(id) {
          popup_visit = sunrise({
            data: {},
            target: '/schedule/popup_visit?id='+id
          })
        },
        pagination_page(page) {
          CORE.set_url_parameter({page: page, name: this.name});
        },
      }
    });

    FRONT.mount('#container');
  </script>
</div>

<? include VIEWS_PATH.'/_include/foot.php'; ?>
