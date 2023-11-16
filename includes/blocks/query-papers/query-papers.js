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
  

  wp.blocks.registerBlockVariation( 'core/query', {
      name: 'augustin/sorted-article-list',
      title: 'Augustin Articles List',
      description: 'Displays a list of articles',
      isActive: ( { namespace, query } ) => {
          return (
              namespace === 'augustin/sorted-article-list'
              && query.postType === 'articles'

          );
      },
      attributes: {
          namespace: 'augustin/sorted-article-list',
          query: {
              perPage: 6,
              pages: 0,
              offset: 0,
              postType: 'articles',
              order: 'desc',
              orderBy: 'date',
              author: '',
              search: '',
              exclude: [],
              sticky: '',
              inherit: false,
          },
      },
      innerBlocks: [
        [
            'core/post-template',
            {},
            [ [ 'core/post-title' ], [ 'core/post-excerpt' ] ],
        ],
        [ 'core/query-pagination' ],
        [ 'core/query-no-results' ],
    ],
      scope: [ 'inserter' ],
      }
  );
  
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


