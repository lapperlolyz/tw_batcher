const nw = "100";

const fields = {
    all: [
        {text: 'id', value: 'id', width: nw, default: 1, immutable: 1},
        {text: 'template', value: 'template', width: nw, type: 'list', binding: 'templates',
            itemText: (v) => `${v.templatename} (${v.id})`,
            itemValue: "id",
            default: 1
        },
        {text: 'context_key', value: 'context_key', type: 'list', binding: 'contexts',
            itemText: (v) => `${v.name} (${v.key})`,
            itemValue: "key",
            default: 1
        },
        {text: 'pagetitle', value: 'pagetitle', default: 1},

        {text: 'alias', value: 'alias'},
        {text: 'alias_visible', value: 'alias_visible', width: nw},
        {text: 'cacheable', value: 'cacheable'},
        {text: 'class_key', value: 'class_key'},
        {text: 'content', value: 'content'},
        {text: 'contentType', value: 'contentType'},
        {text: 'content_dispo', value: 'content_dispo'},
        {text: 'content_type', value: 'content_type'},
        {text: 'createdby', value: 'createdby'},
        {text: 'createdon', value: 'createdon'},
        {text: 'deleted', value: 'deleted'},
        {text: 'deletedby', value: 'deletedby'},
        {text: 'deletedon', value: 'deletedon'},
        {text: 'description', value: 'description'},
        {text: 'donthit', value: 'donthit'},
        {text: 'editedby', value: 'editedby'},
        {text: 'editedon', value: 'editedon'},
        {text: 'hidemenu', value: 'hidemenu'},
        {text: 'hide_children_in_tree', value: 'hide_children_in_tree'},
        {text: 'introtext', value: 'introtext'},
        {text: 'isfolder', value: 'isfolder'},
        {text: 'link_attributes', value: 'link_attributes'},
        {text: 'longtitle', value: 'longtitle'},
        {text: 'menuindex', value: 'menuindex'},
        {text: 'menutitle', value: 'menutitle'},
        {text: 'parent', value: 'parent', width: nw},
        {text: 'privatemgr', value: 'privatemgr'},
        {text: 'privateweb', value: 'privateweb'},
        {text: 'properties', value: 'properties'},
        {text: 'published', value: 'published', width: nw},
        {text: 'publishedby', value: 'publishedby'},
        {text: 'publishedon', value: 'publishedon'},
        {text: 'pub_date', value: 'pub_date'},
        {text: 'richtext', value: 'richtext', width: nw},
        {text: 'searchable', value: 'searchable'},
        {text: 'show_in_tree', value: 'show_in_tree', width: nw},
        {text: 'type', value: 'type'},
        {text: 'unpub_date', value: 'unpub_date'},
        {text: 'uri', value: 'uri'},
        {text: 'uri_override', value: 'uri_override'},
    ]
}

fields.default = fields.all.filter(f => f.default);

export default fields;

// export default {
//     default: [
//         {text: 'id', value: 'id', width: nw},
//         {text: 'template', value: 'template', width: nw},
//         {text: 'context_key', value: 'context_key'},
//         {text: 'pagetitle', value: 'pagetitle'}
//     ]
// }