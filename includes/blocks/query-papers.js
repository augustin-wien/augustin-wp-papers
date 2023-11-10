function register_newspapers_articles_block(){
  const { registerBlockType } = wp.blocks;
     
  registerBlockType('augustin/newspaper-articles', {
    title: 'Newspaper Articles',
    category: 'common',
    icon: 'smiley',
    description: 'Learning in progress',
    keywords: ['example', 'test'],
    edit: () => { 
      return "<div>:)</div>" 
    },
    save: () => { 
      return "<div>:)</div>"
    }
  });
}


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
          return   'magazin-1';
        }
      },
    },
  });
  register_newspapers_articles_block();
});


