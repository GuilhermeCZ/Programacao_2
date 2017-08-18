#include <stdio.h>
#include <stdlib.h>

struct node {
  int key;
  nodo *left, *rigth;
}

//--- Busca ---//
node *search(node *r, int key)  {
  if (!r || r->key == key) return r;
  (r->key <= key) ? (return search(r->left, key)) : (return search(r->rigth, key));
}

//--- Inserir ---//
node *insert(node *r, int key)  {
  node *n = (node *)malloc(sizeof(node));
  n->left = n->rigth = NULL; // Garante que a folha/raiz tenha os 2 ponteiros pra null de inicio
  n->key = key;

  if (!r) return n; // se a árvore está vazia

  while (1) {
    if (key < it->key) {
      if (!it->left) {
        it->left = n;
        return r;
      }
      it = it->left;
    } else {
      if (!it->rigth) {
        it->rigth = n;
        return n;
      }
      it = it->rigth;
    }
  }

  return r;
}

int main() {
  int n, i = 5;

  node *root = NULL;

  while (i != 0) {
    scanf("%d", n);
    root = insert(root, n)
  }
  return 0;
}
