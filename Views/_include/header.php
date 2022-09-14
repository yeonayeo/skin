<header id="header">
  <aside id="side">
    <h1 class="logo"><a href="/schedule"><img src="/assets/images/common/logo_sidebar.svg" alt=""></a></h1>
    <div class="inner">
      <nav class="gnb">
        <ul>
          <li v-for="depth1 in gnb_tree" :class="{active: depth1.child_visible}">
            <a :href="depth1.url">{{ depth1.name }}</a>
            <ul>
              <li v-for="depth2 in depth1.depth2" :class="{selected: depth2.is_selected}"><a :href="depth2.url">{{ depth2.name }}</a></li>
            </ul>
          </li>
        </ul>
      </nav>
      <div class="profile">
        <button type="button" class="name_box" @click="action_logout">
          <div class="name">{{user_name}}<span>님</span></div>
        </button>
      </div>
    </div>
  </aside>
</header>

<script>
  RES = <?=json_encode($_RES);?>;
  GET = <?=json_encode($_GET);?>;

  var HEADER = Vue.createApp({
    data() {
      return {
        res: RES,
        cate: document.URL.split('/')[3],
        active_depth: 3,
        gnb_tree: [
          {
            cate: 'schedule',
            name: '예약 관리',
            url: '/schedule'
          },
          {
            cate: 'client',
            name: '고객 정보',
            url: '/client'
          },
          {
            cate: 'setting',
            name: '설정 및 관리',
            url: '/setting/ticket',
            depth2: [
              {name: '이용권 설정', url: '/setting/ticket', is_selected: RES.sub_selected[0]},
              {name: '화장품 관리', url: '/setting/cosmetic', is_selected: RES.sub_selected[1]},
              {name: '비품 관리', url: '/setting/stuff', is_selected: RES.sub_selected[2]},
              {name: '문자 발송', url: '/setting/sms', is_selected: RES.sub_selected[3]},
            ]
          },
        ],
        user_name: RES.user_name,
        is_super: RES.is_super,
        is_client: RES.is_client,
        sub_selected: RES.sub_selected,
      }
    },
    mounted() {
      if(this.is_super === true) {
        this.gnb_tree[2].depth2 = this.gnb_tree[2].depth2.concat([
          {name: '관리자 메뉴', url: '/setting/admin', is_selected: RES.sub_selected[4]},
          {name: '매출 현황', url: '/setting/sales', is_selected: RES.sub_selected[5]},
        ]);
      }

      if(this.cate) {
        for (let depth1 of this.gnb_tree) {
          if (depth1.cate === this.cate) {
            depth1.child_visible = true;
          }
        }
      } else {
        // url 뒤가 없으면 예약관리에 표시가 안되어서 추가
        this.gnb_tree[0].child_visible = true;
      }
    },
    methods: {
      action_logout: function(e) {
        if(confirm('로그아웃 하시겠습니까?')) {
          $.ajax({
            url: '/auth/action_logout',
            data: [],
            success: (res) => {
              if (res.res_cd === 'OK') {
                location.href = '/auth/login';
              } else {
                alert(res.err_msg);
              }
            }
          });
        }
      }
    }
  });

  HEADER.mount('#header');
</script>
