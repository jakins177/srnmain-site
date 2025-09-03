export const categories = [
  { id: 'all', label: 'All' },
  { id: 'html', label: 'HTML' },
  { id: 'css', label: 'CSS' },
  { id: 'javascript', label: 'JavaScript' },
  { id: 'legal', label: 'Legal', soon: true },
  { id: 'real-estate', label: 'Real Estate', soon: true },
  { id: 'medical', label: 'Medical', soon: true },
  { id: 'entertainment', label: 'Entertainment', soon: true },
];

export const personas = [
  {
    id: 'html-master',
    title: 'HTML Master',
    initials: 'HM',
    category: 'html',
    status: 'alpha',
    desc: 'Validate, tidy, and explain your markup. Perfect for beginners and production checklists.',
    tags: ['validation', 'semantics', 'accessibility'],
    actions: ['try','details']
  },
  {
    id: 'css-stylist',
    title: 'CSS Stylist',
    initials: 'CS',
    category: 'css',
    status: 'alpha',
    desc: 'Layout whisperer. From flexbox to grid, plus token systems and theming tips.',
    tags: ['layout', 'grid', 'design tokens'],
    actions: ['try','details']
  },
  {
    id: 'js-debugger',
    title: 'JS Debugger',
    initials: 'JS',
    category: 'javascript',
    status: 'alpha',
    desc: 'Trace errors, suggest fixes, and explain concepts with runnable snippets.',
    tags: ['errors', 'snippets', 'concepts'],
    actions: ['try','details']
  },
  {
    id: 'legal-brief',
    title: 'Legal Brief Buddy',
    initials: 'LB',
    category: 'legal',
    soon: true,
    desc: 'Draft, summarize, and outline legal docs. (Expert review recommended.)',
    tags: ['summaries','templates'],
    actions: ['notify','details']
  },
  {
    id: 'realty-scout',
    title: 'Realty Scout',
    initials: 'RS',
    category: 'real-estate',
    soon: true,
    desc: 'Analyze listings, extract features, and draft descriptions across MLS formats.',
    tags: ['listings','copy'],
    actions: ['notify','details']
  },
  {
    id: 'medinotes',
    title: 'MediNotes Scribe',
    initials: 'MS',
    category: 'medical',
    soon: true,
    desc: 'Clinical note helper for SOAP summaries and patient instructions. (Not medical advice.)',
    tags: ['soap','summaries'],
    actions: ['notify','details']
  },
  {
    id: 'script-muse',
    title: 'Scriptwriter Muse',
    initials: 'SM',
    category: 'entertainment',
    soon: true,
    desc: 'Idea generator and beat‑sheet buddy for shorts, skits, and pilots.',
    tags: ['beats','loglines'],
    actions: ['notify','details']
  },
];