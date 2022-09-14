<? include VIEWS_PATH.'/_include/head.php'; ?>

<div id="wrap">
  <? include VIEWS_PATH.'/_include/header.php'; ?>

  <main id="container" class="sub_container" v-cloak>
    <div class="page_head mb60">
      <h2 class="page_title">설정 및 관리</h2>
      <h3 class="page_subtitle">비품 관리</h3>
    </div>
    <div class="page_contents">
      <div class="search_area">
        <div class="input_search">
          <input type="search" placeholder="비품명으로 검색하세요" style="width: 360px;" v-model="name" @keypress.enter="action_search">
          <button type="button" class="btn_input_search" @click="action_search"></button>
        </div>
        <div class="btns">
          <div>
            <button type="button" class="btn_download" @click="excel_download"></button> <!-- 0719작업 -->
            <button type="button" class="btn e2 s ml10" style="width: 100px;" @click="action_delete">비품 삭제</button>
          </div>
          <button type="button" class="btn c1 l btn_regist" style="width: 200px;" @click="popup_regist()">비품 등록</button>
        </div>
      </div>
      <div class="result_txt">총 {{total_cnt}}개</div>
      <div class="table_list">
        <table>
          <colgroup>
            <col style="width: 50px;">
            <col style="width: 400px;">
            <col style="width: 300px;">
            <col style="width: 300px;">
            <col style="width: auto;">
          </colgroup>
          <thead>
            <tr>
              <th><!-- 빈 태그 --></th>
              <th>비품명</th>
              <th>재고</th>
              <th>구분</th>
              <th>메모</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="total_cnt!='0'" v-for="stuff in list" @click="link_detail(stuff.id)">
              <td>
                <label class="checkbox" @click.stop>
                  <input type="checkbox" v-model="stuff.checked">
                  <span></span>
                </label>
              </td>
              <td>{{ stuff.name }}</td>
              <td>{{ stuff.remain_quantity }}개</td>
              <td>{{ stuff.type }}</td>
              <td>{{ stuff.memo }}</td>
            </tr>
            <tr class="list_empty" v-if="total_cnt=='0'">
              <td colspan="5">검색 결과가 없습니다.</td>
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
          pagination: RES.pagination,
        }
      },
      mounted() {},
      methods: {
        popup_regist() {
          popup_regist = sunrise({
            data: {},
            target: '/setting/stuff/popup_regist'
          })
        },
        action_search() {
          CORE.set_url_parameter({page: 1, name: this.name});
        },
        pagination_page(page) {
          CORE.set_url_parameter({page: page, name: this.name});
        },
        link_detail(id) {
          location.href = '/setting/stuff/detail?id=' + id;
        },
        action_delete(type) {
          let selected = [];
          for (let stuff of this.res.list) {
            if (stuff.checked) {
              selected.push(stuff.id);
            }
          }
          if (!selected.length) return alert('비품 먼저 선택해주세요.');
          if(!confirm('비품 정보 삭제시 복구할 수 없습니다.\n정말 삭제하시겠습니까?')) return;

          $.ajax({
            url: '/setting/stuff/action_delete',
            data: {id: selected},
            success: (res) => {
              if (res.res_cd === 'OK') {
                location.href = `/setting/stuff`;
              } else {
                alert(res.err_msg);
              }
            }
          });
        },
        excel_download() {
          window.open("/setting/stuff/excel_download", "_blank");
        }
      }
    });

    FRONT.mount('#container');
  </script>
</div>

<? include VIEWS_PATH.'/_include/foot.php'; ?>
