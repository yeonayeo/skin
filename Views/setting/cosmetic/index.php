<? include VIEWS_PATH.'/_include/head.php'; ?>

<div id="wrap">
  <? include VIEWS_PATH.'/_include/header.php'; ?>

  <main id="container" class="sub_container" v-cloak>
    <div class="page_head mb60">
      <h2 class="page_title">설정 및 관리</h2>
      <h3 class="page_subtitle">화장품 관리</h3>
    </div>
    <div class="page_contents">
      <div class="search_area">
        <div class="input_search">
          <input type="search" placeholder="제품명으로 검색하세요" style="width: 360px;" v-model="name" @keypress.enter="action_search">
          <button type="button" class="btn_input_search" @click="action_search"></button>
        </div>
        <div class="btns">
          <div>
            <button type="button" class="btn_download" @click="excel_download"></button> <!-- 0719작업 -->
            <button type="button" class="btn c4 s ml10" style="width: 100px;" @click="action_use('Y')">활성화</button>
            <button type="button" class="btn e4 s ml5" style="width: 100px;" @click="action_use('N')">비활성화</button>
          </div>
          <button type="button" class="btn c1 l btn_regist" style="width: 200px;" @click="popup_regist()">제품 등록</button>
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
              <th>제품명</th>
              <th>재고</th>
              <th>판매가</th>
              <th>메모</th> <!-- 0719작업 -->
            </tr>
          </thead>
          <tbody>
            <tr v-for="cosmetic in list" v-if="total_cnt>0" @click="link_detail(cosmetic.id)" :class="cosmetic.class">
              <td>
                <label class="checkbox" @click.stop>
                  <input type="checkbox" v-model="cosmetic.checked">
                  <span></span>
                </label>
              </td>
              <td class="ta_l" style="padding: 18px;">{{ cosmetic.name }}</td>
              <td>{{ cosmetic.remain_quantity}}개</td>
              <td>{{ cosmetic.sales_price }}원</td>
              <!-- <td>{{ cosmetic.fee }}원</td> -->
              <td>-</td> <!-- 0719작업 -->
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
            target: '/setting/cosmetic/popup_regist'
          })
        },
        action_search() {
          CORE.set_url_parameter({page: 1, name: this.name});
        },
        pagination_page(page) {
          CORE.set_url_parameter({page: page, name: this.name});
        },
        link_detail(id) {
          location.href = '/setting/cosmetic/detail?id=' + id;
        },
        action_use(type) {
          let selected = [];
          for (let cosmetic of this.res.list) {
            if (cosmetic.checked) {
              selected.push(cosmetic.id);
            }
          }

          if (!selected.length) return alert('제품을 먼저 선택해주세요.');

          $.ajax({
            url: '/setting/cosmetic/action_use',
            data: {is_use: type, ids: selected},
            success: (res) => {
              if (res.res_cd === 'OK') {
                location.href = `/setting/cosmetic`;
              } else {
                alert(res.err_msg);
              }
            }
          });
        },
        excel_download() {
          window.open("/setting/cosmetic/excel_download", "_blank"); 
        }
      }
    });

    FRONT.mount('#container');
  </script>
</div>

<? include VIEWS_PATH.'/_include/foot.php'; ?>
