<? include VIEWS_PATH.'/_include/head.php'; ?>

<div id="wrap">
  <? include VIEWS_PATH.'/_include/header.php'; ?>

  <main id="container" class="sub_container" v-cloak>
    <h2 class="page_title mb60">고객 정보</h2>
    <div class="page_contents">
      <div class="search_area">
        <div class="input_search">
          <input type="search" placeholder="고객명을 검색하세요" style="width: 360px;" v-model="name" @keypress.enter="action_search">
          <button type="button" class="btn_input_search" @click="action_search"></button>
        </div>
        <div class="btns">
          <button type="button" class="btn e2 s" style="width: 100px;" @click="action_delete">고객 삭제</button>
          <button type="button" class="btn c1 l btn_regist" style="width: 200px;" @click="popup_regist()">고객 등록</button>
        </div>
      </div>
      <div class="result_txt">총 {{total_cnt}}명</div>
      <div class="table_list">
        <table>
          <colgroup>
            <col style="width: 50px;">
            <col style="width: 220px;">
            <col style="width: 220px;">
            <col style="width: 220px;">
            <col style="width: 160px;">
            <col style="width: 220px;">
            <col style="width: auto;">
          </colgroup>
          <thead>
            <tr>
              <th><!-- 빈 태그 --></th>
              <th>고객명</th>
              <th>연락처</th>
              <th>생년월일</th>
              <th>성별</th>
              <th>최근 방문일</th>
              <th>사용 중인 이용권</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="client in list" v-if="total_cnt>0" @click="link_detail(client.id)">
              <td>
                <label class="checkbox" @click.stop>
                  <input type="checkbox" v-model="client.checked">
                  <span></span>
                </label>
              </td>
              <td>{{ client.name }}</td>
              <td>{{ client.hp }}</td>
              <td>{{ client.birth }}</td>
              <td>{{ client.gender }}</td>
              <td>{{ client.recently_visit_date }}</td>
              <td class="ta_l">{{ client.use_ticket }}</td>
            </tr>
            <!-- 결과없음 -->
            <tr class="list_empty" v-if="total_cnt<=0">
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
          get: GET,
          req: {},
          err: {},
          page: RES.page,
          total_cnt: RES.total_cnt,
          list: RES.list,
          name: RES.name,
          pagination: RES.pagination
        }
      },
      mounted() {},
      methods: {
        popup_regist() {
          popup_regist = sunrise({
            data: {},
            target: '/client/popup_regist'
          })
        },
        link_detail(id) {
          location.href = '/client/detail?id=' + id;
        },
        action_search() {
          CORE.set_url_parameter({page: 1, name: this.name});
        },
        pagination_page(page) {
          CORE.set_url_parameter({page: page, name: this.name});
        },
        action_delete() {
          var selected = [];

          for (let client of this.res.list) {
            if (client.checked) {
              selected.push(client.id);
            }
          }
          if (!selected.length) return alert('삭제할 고객을 선택해주세요');
          if(!confirm('고객 정보 삭제시 복구할 수 없습니다.\n정말 삭제하시겠습니까?')) return;
          $.ajax({
            url: '/client/action_delete',
            data: {ids: selected},
            success: (res) => {
              if (res.res_cd === 'OK') {
                location.href = `/client`;
              } else {
                alert(res.err_msg);
              }
            }
          });
        }
      }
    });

    FRONT.mount('#container');
  </script>
</div>

<? include VIEWS_PATH.'/_include/foot.php'; ?>
