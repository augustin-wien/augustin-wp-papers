wp.domReady(function () {
  console.log('Registering block variation: augustin/papers-list');
  wp.blocks.registerBlockVariation('core/query', {
    name: 'augustin/papers-list',
    title: 'Papers List',
    attributes: {
      query: {
        postType: 'papers',
      },
    },
  });
  wp.blocks.registerBlockVariation('core/query', {
    name: 'augustin/articles-list',
    title: 'Articles List',
    attributes: {
      query: {
        postType: 'post',
        category: function () {
            console.log("blub");
          return   'Augustin';
        }
      },
    },
  });
});
