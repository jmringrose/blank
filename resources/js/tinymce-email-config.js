export const emailEditorConfig = {
  height: 500,
  menubar: false,
  plugins: 'advlist autolink lists link image charmap preview anchor code fullscreen insertdatetime media table help wordcount',
  toolbar: 'undo redo | formatselect | bold italic underline | alignleft aligncenter alignright | bullist numlist | link image tables | code',

  // Email-optimized formats with inline styles
  formats: {
    bold: {inline: 'strong'},
    italic: {inline: 'em'},
    underline: {inline: 'span', styles: {textDecoration: 'underline'}},
    alignleft: {block: 'p', styles: {textAlign: 'left'}},
    aligncenter: {block: 'p', styles: {textAlign: 'center'}},
    alignright: {block: 'p', styles: {textAlign: 'right'}},
    h1: {block: 'h1', styles: {fontSize: '24px', fontWeight: 'bold', margin: '0 0 16px 0', fontFamily: 'Arial, sans-serif'}},
    h2: {block: 'h2', styles: {fontSize: '20px', fontWeight: 'bold', margin: '0 0 14px 0', fontFamily: 'Arial, sans-serif'}},
    h3: {block: 'h3', styles: {fontSize: '18px', fontWeight: 'bold', margin: '0 0 12px 0', fontFamily: 'Arial, sans-serif'}},
    p: {block: 'p', styles: {margin: '0 0 16px 0', fontFamily: 'Arial, sans-serif', fontSize: '14px', lineHeight: '1.4'}}
 },

  // Custom style formats for emails
  style_formats: [
    {title: 'Paragraph', format: 'p'},
    {title: 'Heading 1', format: 'h1'},
    {title: 'Heading 2', format: 'h2'},
    {title: 'Heading 3', format: 'h3'}
  ],

  content_style: `
    body { font-family: Arial, sans-serif; font-size: 14px; line-height: 1.4; margin: 0; padding: 20px; }
    p { margin: 0 0 16px 0; font-family: Arial, sans-serif; font-size: 14px; line-height: 1.4; }
    h1, h2, h3 { margin: 0 0 16px 0; font-family: Arial, sans-serif; }
    h1 { font-size: 24px; font-weight: bold; }
    h2 { font-size: 20px; font-weight: bold; }
    h3 { font-size: 18px; font-weight: bold; }
    ul, ol { margin: 0 0 16px 0; padding-left: 30px; }
    li { margin-bottom: 8px; }
    a { color: #007cba; text-decoration: underline; }
  `,

  style_formats_merge: false,

  // Email-safe settings
  forced_root_block: 'p',
  force_p_newlines: true,
  remove_trailing_brs: true,

  // Prevent TinyMCE from adding CSS classes
  valid_elements: '*[style]',
  extended_valid_elements: '*[style]'
};
