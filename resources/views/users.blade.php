<table>
  <thead>
    <tr>
      <th>ID #</th>
      <th>Name</th>
      <th>Email</th>
    </tr>
  </thead>
  <tbody>
    @foreach ($data as $user)
    <tr>
      <td>{{ $user->id }}</td>
      <td>{{ $user->name }}</td>
      <td>{{ $user->email }}</td>
    </tr>
    @endforeach
  </tbody>
</table>