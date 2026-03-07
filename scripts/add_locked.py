import os
import re

livewire_dir = os.path.join('app', 'Livewire')

MODEL_TYPES = [
    'Classroom', 'Assignment', 'Submission', 'User',
    'Announcement', 'Topic', 'AttendanceSession',
    'Badge', 'Achievement', 'StoreItem', 'BugReport',
]

PROP_PATTERN = re.compile(
    r'^(\s+)(public (?:' + '|'.join(MODEL_TYPES) + r') \$\w+;)',
    re.MULTILINE,
)

LOCKED_IMPORT = 'use Livewire\\Attributes\\Locked;'

fixed = []

for root, dirs, files in os.walk(livewire_dir):
    for fname in files:
        if not fname.endswith('.php'):
            continue
        path = os.path.join(root, fname)
        with open(path, 'r', encoding='utf-8') as f:
            original = f.read()

        lines = original.split('\n')
        new_lines = []
        modified = False

        for i, line in enumerate(lines):
            m = PROP_PATTERN.match(line)
            if m:
                # Check if previous non-blank line already has #[Locked]
                prev = ''
                for j in range(len(new_lines) - 1, -1, -1):
                    if new_lines[j].strip():
                        prev = new_lines[j].strip()
                        break
                if prev != '#[Locked]':
                    indent = m.group(1)
                    new_lines.append(indent + '#[Locked]')
                    modified = True
            new_lines.append(line)

        if not modified:
            continue

        new_content = '\n'.join(new_lines)

        # Add import if missing
        if LOCKED_IMPORT not in new_content:
            # Try to insert after last Livewire Attributes import
            new_content, n = re.subn(
                r'(use Livewire\\Attributes\\[A-Za-z]+;)',
                lambda m: m.group(0) + '\n' + LOCKED_IMPORT,
                new_content, count=1
            )
            if n == 0:
                # Fallback: insert after 'use Livewire\Component;'
                new_content = new_content.replace(
                    'use Livewire\\Component;',
                    'use Livewire\\Attributes\\Locked;\nuse Livewire\\Component;',
                    1
                )

        with open(path, 'w', encoding='utf-8') as f:
            f.write(new_content)

        rel = os.path.relpath(path, livewire_dir)
        fixed.append(rel)

print(f'Fixed {len(fixed)} files:')
for f in fixed:
    print(f'  + {f}')
