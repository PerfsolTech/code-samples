Nova.booting((Vue, router, store) => {
  router.addRoutes([
    {
      name: 'qr-codes',
      path: '/qr-codes',
      component: require('./components/Tool'),
    },
  ])
})
