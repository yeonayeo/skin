<? include VIEWS_PATH.'/_include/head.php'; ?>

<div id="wrap">
  <? include VIEWS_PATH.'/_include/header.php'; ?>

  <main id="container" class="sub_container" v-cloak>
    <div class="page_head mb60">
      <h2 class="page_title">설정 및 관리</h2>
      <h3 class="page_subtitle">이용권 설정</h3>
    </div>
    <div class="page_contents">
      <div class="search_area">
        <div class="input_search">
          <input type="search" placeholder="이용권명으로 검색하세요" style="width: 360px;" v-model="name" @keypress.enter="action_search">
          <button type="button" class="btn_input_search" @click="action_search"></button>
        </div>
        <div class="btns">
          <div>
            <button type="button" class="btn_download" @click="excel_download"></button> <!-- 0719작업 -->
            <button type="button" class="btn c4 s ml10" style="width: 100px;" @click="action_use('Y')">활성화</button>
            <button type="button" class="btn e4 s ml5" style="width: 100px;" @click="action_use('N')">비활성화</button>
          </div>
          <button type="button" class="btn c1 l btn_regist" style="width: 200px;" @click="popup_regist()">이용권 등록</button>
        </div>
      </div>
      <div class="result_txt">총 {{total_cnt}}개</div>
      <div class="table_list">
        <table>
          <colgroup>
            <col style="width: 50px;">
            <col style="width: 400px;">
            <col style="width: 500px;">
            <col style="width: auto;">
          </colgroup>
          <thead>
            <tr>
              <th><!-- 빈 태그 --></th>
              <th>이용권명</th>
              <th>종류</th>
              <th>비고</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="ticket in list" v-if="total_cnt>0" @click="popup_detail(ticket.id)" :class="ticket.class">
              <td>
                <label class="checkbox" @click.stop>
                  <input type="checkbox" v-model="ticket.checked">
                  <span></span>
                </label>
              </td>
              <td class="ta_l" style="padding: 18px;">{{ticket.name}}</td>
              <td v-html="ticket.kind"></td>
              <td>{{ticket.note}}</td>
            </tr>
            <tr class="list_empty" v-if="total_cnt<=0">
              <td colspan="5">검색 결과가 없습니다.</td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="pagination" v-if="res.pagination.page_range.length">
        <div class="navi">
          <button type="button" @click="pagination_page(res.pagination.prev_page)"><i class="material-icons">navigate_before</i></button>
        </div>
        <div class="pages">
          <button type="button" @click="pagination_page(page)" :class="{on: res.pagination.page == page}" v-for="page in res.pagination.page_range">{{ page }}</button>

        </div>
        <div class="navi">
          <button type="button" @click="pagination_page(res.pagination.next_page)"><i class="material-icons">navigate_next</i></button>
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
          name: RES.name
        }
      },
      mounted() {},
      methods: {
        popup_regist() {
          popup_regist = sunrise({
            data: {},
            target: '/setting/ticket/popup_regist'
          })
        },
        popup_detail(id) {
          popup_detail = sunrise({
            data: {},
            target: '/setting/ticket/popup_detail?id='+id
          })
        },
        action_search() {
          CORE.set_url_parameter({page: 1, name: this.name});
        },
        pagination_page(page) {
          CORE.set_url_parameter({page: page, name: this.name});
        },
        action_use(type) {
          let selected = [];
          for (let ticket of this.res.list) {
            if (ticket.checked) {
              selected.push(ticket.id);
            }
          }

          if (!selected.length) return alert('이용권을 먼저 선택해주세요.');

          $.ajax({
            url: '/setting/ticket/action_use',
            data: {is_use: type, ids: selected},
            success: (res) => {
              if (res.res_cd === 'OK') {
                location.href = `/setting/ticket`;
              } else {
                alert(res.err_msg);
              }
            }
          });
        },
        excel_download() {
          window.open("/setting/ticket/excel_download", "_blank"); 
        }
      }
    });

    FRONT.mount('#container');
  </script>
</div>

<? include VIEWS_PATH.'/_include/foot.php'; ?>
