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
    details: `
      <p>Discover HTML Master – Your Interactive Path to Becoming an HTML Pro</p>
      <p>Ready to finally master HTML without boring textbooks or endless tutorials? HTML Master is a hands-on, interactive course built right into your browser. From your very first tag to advanced, real-world projects, you’ll learn step by step while actually coding along the way.</p>
      <h4>💡 Why learners love it:</h4>
      <ul>
        <li><strong>Learn by Doing:</strong> Every lesson includes live code editors, previews, and quizzes so you can instantly see your progress.</li>
        <li><strong>Three Levels of Mastery:</strong> Start with the Basics, build confidence with Intermediate, and tackle real projects in the Advanced module.</li>
        <li><strong>Track Your Progress:</strong> See exactly how far you’ve come with built-in progress tracking—your completions and quiz scores update automatically.</li>
        <li><strong>Your AI Coding Buddy:</strong> Stuck on a concept? Our AI Assistant (powered by n8n) is right there in the chat widget, ready to answer your HTML questions in real time.</li>
        <li><strong>Fair Credit System:</strong> The chat runs on Gasergy Credits, so you’re always in control. Need more? Recharge instantly with secure Stripe checkout.</li>
      </ul>
      <p><strong>🔒 Members-Only Access:</strong><br />Everything lives behind a simple login, so your progress and tools are saved just for you. Once you’re in, it’s your personal learning hub.</p>
      <p>👉 Whether you’re brand new or looking to sharpen your skills, HTML Master makes learning fun, practical, and accessible.<br />Try it now and start coding your first project today!</p>
    `,
    tags: ['validation', 'semantics', 'accessibility'],
    actions: ['try','details']
  },
  {
    id: 'css-stylist',
    title: 'CSS Stylist',
    initials: 'CS',
    category: 'css',
    soon: true,
    desc: 'Layout whisperer. From flexbox to grid, plus token systems and theming tips.',
    tags: ['layout', 'grid', 'design tokens'],
    actions: ['notify','details']
  },
  {
    id: 'js-debugger',
    title: 'JS Debugger',
    initials: 'JS',
    category: 'javascript',
    soon: true,
    desc: 'Trace errors, suggest fixes, and explain concepts with runnable snippets.',
    tags: ['errors', 'snippets', 'concepts'],
    actions: ['notify','details']
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
