def parse_yaml(yaml_string):
    data = {}
    lines = yaml_string.splitlines()
    current_key = None
    current_dict = data

    for line in lines:
        line = line.strip()
        if not line:
            continue

        if line.startswith('#'):
            continue

        if line.startswith('- '):
            # List item
            if 'list' not in current_dict:
                current_dict['list'] = []
            current_dict['list'].append(line[2:].strip())
            continue

        if ':' in line:
            key, value = line.split(':', 1)
            key = key.strip()
            value = value.strip()

            if value.startswith('{'):
                # Nested dictionary
                current_dict[key] = {}
                current_key = key
                current_dict = current_dict[key]
            else:
                current_dict[key] = value
        else:
            # Indented line, assume it's a value for the current key
            if current_key:
                if isinstance(current_dict[current_key], dict):
                    # Add to existing dictionary
                    pass
                else:
                    current_dict[current_key] += ' ' + line.strip()

    return data

def read_yaml_from_file(file_path):
    try:
        with open(file_path, 'r') as file:
            yaml_string = file.read()
            return parse_yaml(yaml_string)
    except FileNotFoundError:
        print(f"File not found: {file_path}")
        return None

# Example usage:
file_path = '../configs/selloFecytJson.yaml'
data = read_yaml_from_file(file_path)
print(data)