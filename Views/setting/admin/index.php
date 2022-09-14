<? include VIEWS_PATH.'/_include/head.php'; ?>

<div id="wrap">
  <? include VIEWS_PATH.'/_include/header.php'; ?>

  <main id="container" class="sub_container" v-cloak>
    <div class="page_head mb60">
      <h2 class="page_title">설정 및 관리</h2>
      <h3 class="page_subtitle">관리자 메뉴</h3>
    </div>
    <div class="setting_admin_wrap">
      <section class="list_area">
        <div class="area_top">
          <div class="flex_area jc_sb ai_c mb20">
            <div class="area_title mb0">직원 정보</div>
            <button type="button" class="btn e1 s" style="width: 100px;" @click="popup_regist()">직원 추가</button>
          </div>
          <div class="table_list">
            <table>
              <colgroup>
                <col style="width: 100px;">
                <col style="width: 200px;">
                <col style="width: 230px;">
                <col style="width: 320px;">
                <col style="width: 200px;">
                <col style="width: auto;">
              </colgroup>
              <thead>
                <tr>
                  <th>번호</th>
                  <th>관리자명</th>
                  <th>구분</th>
                  <th>연락처</th>
                  <th>로그인 코드</th>
                  <th>비고</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="admin in admin_list" v-if="admin_list.length" @click="popup_detail(admin.id)">
                  <td>{{ admin.no }}</td>
                  <td>{{ admin.name }}<em class="icon_master" v-if="admin.is_super"></em></td>
                  <td>{{ admin.position }}</td>
                  <td>{{ admin.hp }}</td>
                  <td>{{ admin.login_cd }}</td>
                  <td>{{ admin.note }}</td>
                </tr>
                <tr class="list_empty" v-if="!admin_list.length">
                  <td colspan="6">직원 정보가 없습니다.</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div class="area_bottom">
          <div class="area_title">비밀번호 관리</div>
          <button type="button" class="btn c1 l" style="width: 200px;" @click="popup_update_password()">고객 정보 비밀번호</button>
        </div>
      </section>
      <section class="calendar_area">
        <div class="area_title">공지사항</div>
        <div class="inner">
          <div class="calendar_box">
            <div class="calendar_navi">
              <button type="button" class="navi" @click="get_calendar(calendar_info.prev_y, calendar_info.prev_m)"><i class="material-icons">keyboard_arrow_left</i></button>
              <div class="month_text">{{calendar_info.year}}년 {{calendar_info.month_txt}}월</div>
              <button type="button" class="navi" @click="get_calendar(calendar_info.next_y, calendar_info.next_m)"><i class="material-icons">keyboard_arrow_right</i></button>
            </div>
            <div class="calendar_body">
              <table>
                <thead>
                  <tr>
                    <th>S</th>
                    <th>M</th>
                    <th>T</th>
                    <th>W</th>
                    <th>T</th>
                    <th>F</th>
                    <th>S</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(week, index) in calendar_info.calendar" :key="index">
                    <td v-for="(date, idx) in week.date_list" :key="idx" :class="date.class" @click="selected_date(date.ymd, index, idx)">{{date.date}}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <div class="notice_box mt20">
            <div class="title">스킨스타 공지사항</div>
            <div class="content">
              <textarea placeholder="공지사항을 입력해주세요." v-model="notice.contents"></textarea>
            </div>
            <button type="button" class="btn c1 l mt15" @click="action_notice_update">공지사항 저장</button>
          </div>
        </div>
      </section>
    </div>
  </main>

  <script>
    var FRONT = Vue.createApp({
      data() {
        return {
          res: RES,
          req: {},
          err: {},
          admin_list: RES.admin_list,
          calendar_info: RES.calendar_info,
          ymd: RES.ymd,
          notice: RES.notice
        }
      },
      mounted() {},
      methods: {
        popup_detail(id) {
          popup_detail = sunrise({
            data: {},
            target: '/setting/admin/popup_detail?id='+id
          })
        },
        popup_regist() {
          popup_regist = sunrise({
            data: {},
            target: '/setting/admin/popup_regist'
          })
        },
        popup_update_password() {
          popup_update_password = sunrise({
            data: {},
            target: '/setting/admin/popup_update_password'
          })
        },
        get_calendar(year, month) {
          $.ajax({
            url: '/search/calendar',
            data: {
              year: year,
              month: month
            },
            success: (res) => {
              if (res.res_cd === 'OK') {
                this.calendar_info = res.data;
              } else {
                console.log(res);
              }
            }
          });
        },
        selected_date(ymd, week_idx, dt_idx) {
          for (let week of this.calendar_info.calendar) {
            for (let dt of week.date_list) {
              if(dt.is_today) {
                dt.class = '';
                dt.is_today = false;
              }
              if(dt.ymd == ymd) {
                dt.class = 'today';
                dt.is_today = true;
              }
            }
          }
          this.ymd = ymd;

          // 공지사항 데이터 가져오기
          $.ajax({
            url: '/setting/admin/get_notice_info',
            data: {
              notice_date: this.ymd
            },
            success: (res) => {
              if (res.res_cd === 'OK') {
                this.notice = res.data;
              } else {
                console.log(res);
              }
            }
          });
        },
        action_notice_update() {
          $.ajax({
            url: '/setting/admin/action_notice_update',
            data: {
              notice_date: this.ymd,
              id: this.notice.id,
              contents: this.notice.contents
            },
            success: (res) => {
              if (res.res_cd === 'OK') {
                alert('공지사항이 저장되었습니다');
              } else {
                console.log(res);
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
