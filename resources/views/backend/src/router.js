import Vue from "vue";
import VueRouter from "vue-router";
import AuthRequired from "./utils/AuthRequired";

Vue.use(VueRouter);

const routes = [
  {
    path: "/",
    component: () => import(/* webpackChunkName: "app" */ "./pages"),
    redirect: "/app/dashboards",
    beforeEnter: AuthRequired,
    children: [
      {
        path: "app/dashboards",
        component: () =>
          import(/* webpackChunkName: "dashboards" */ "./pages/Dashboard"),
        meta: {
          title: 'Dashboard - Stikes'
        }
      },
      {
        path: "app/assign-dosen",
        component: () =>
          import(/* webpackChunkName: "assign-dosen" */ "./pages/assignDosen"),
        meta: {
          title: 'Assign Dosen - Stikes'
        }
      },
      {
        path: "app/input-textbook",
        component: () =>
          import(/* webpackChunkName: "input-textbook" */ "./pages/inputTextbook"),
          meta: {
            title: 'Input Textbook - Stikes'
          }
      },
      {
        path: "app/review-approved-textbook",
        component: () =>
          import(/* webpackChunkName: "review-approved-textbook" */ "./pages/reviewApproveTextbook"),
          meta: {
            title: 'Review & Approved Textbook - Stikes'
          }
      },
      {
        path: "app/entry-rps",
        component: () =>
          import(/* webpackChunkName: "entry-rps" */ "./pages/entryRps"),
          meta: {
            title: 'Entry RPS - Stikes'
          }
      },
      {
        path: "app/entry-or",
        component: () =>
          import(/* webpackChunkName: "entry-or" */ "./pages/entryOr"),
          meta: {
            title: 'Entry OR - Stikes'
          }
      },
      {
        path: "app/review-approval-rps",
        component: () =>
          import(/* webpackChunkName: "review-approval-rps" */ "./pages/reviewApprovalRps"),
          meta: {
            title: 'Review & Approval RPS - Stikes'
          }
      },
      {
        path: "app/review-approval-or",
        component: () =>
          import(/* webpackChunkName: "review-approval-or" */ "./pages/reviewApprovalOr"),
          meta: {
            title: 'Review & Approval OR - Stikes'
          }
      },
      {
        path: "app/user-management",
        component: () =>
          import(/* webpackChunkName: "user-management" */ "./pages/userManagement"),
          meta: {
            title: 'User Management - Stikes'
          }
      },
      {
        path: "app/icon",
        component: () =>
          import(/* webpackChunkName: "blank-page" */ "./pages/Icons"),
          meta: {
            title: 'Icons - Stikes'
          }
      }
    ]
  },
  {
    path: "/error",
    component: () => import(/* webpackChunkName: "error" */ "./pages/Error")
  },
  {
    path: "/user",
    component: () => import(/* webpackChunkName: "user" */ "./pages/Auth"),
    redirect: "/user/login",
    children: [
      {
        path: "login",
        component: () =>
          import(/* webpackChunkName: "user" */ "./pages/Auth/Login")
      },
    ]
  },
  {
    path: "*",
    component: () => import(/* webpackChunkName: "error" */ "./pages/Error")
  }
];

const router = new VueRouter({
  linkActiveClass: "active",
  routes,
  mode: "history"
});

// This callback runs before every route change, including on page load.
router.beforeEach((to, from, next) => {
  // This goes through the matched routes from last to first, finding the closest route with a title.
  // e.g., if we have `/some/deep/nested/route` and `/some`, `/deep`, and `/nested` have titles,
  // `/nested`'s will be chosen.
  const nearestWithTitle = to.matched.slice().reverse().find(r => r.meta && r.meta.title);

  // Find the nearest route element with meta tags.
  const nearestWithMeta = to.matched.slice().reverse().find(r => r.meta && r.meta.metaTags);

  // If a route with a title was found, set the document (page) title to that value.
  if(nearestWithTitle) document.title = nearestWithTitle.meta.title;

  // Remove any stale meta tags from the document using the key attribute we set below.
  Array.from(document.querySelectorAll('[data-vue-router-controlled]')).map(el => el.parentNode.removeChild(el));

  // Skip rendering meta tags if there are none.
  if(!nearestWithMeta) return next();

  // Turn the meta tag definitions into actual elements in the head.
  nearestWithMeta.meta.metaTags.map(tagDef => {
    const tag = document.createElement('meta');

    Object.keys(tagDef).forEach(key => {
      tag.setAttribute(key, tagDef[key]);
    });

    // We use this to track which meta tags we create so we don't interfere with other ones.
    tag.setAttribute('data-vue-router-controlled', '');

    return tag;
  })
  // Add the meta tags to the document head.
  .forEach(tag => document.head.appendChild(tag));

  next();
});

export default router;
